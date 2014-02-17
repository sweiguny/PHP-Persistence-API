<?php

namespace PPA\core\query;

use DomainException;
use PDO;
use PDOStatement;
use PPA\core\Entity;
use PPA\core\EntityFactory;
use PPA\core\EntityMetaDataMap;
use PPA\core\mock\MockEntity;
use PPA\core\mock\MockEntityList;
use PPA\core\relation\ManyToMany;
use PPA\core\relation\OneToMany;
use PPA\core\relation\OneToOne;

class TypedQuery extends Query {
    
    protected $classname;
    protected $metaDataMap;

    public function __construct($query, $fullyQualifiedClassname) {
        parent::__construct($query);
        
        # TODO: exclude also DDLs
        if (in_array($this->type, array("update", "insert", "delete"))) {
            throw new DomainException("Cannot be an UPDATE- or INSERT- or DELETE-statement.");
        }
        
        $this->classname   = trim($fullyQualifiedClassname);
        $this->metaDataMap = EntityMetaDataMap::getInstance();
    }
    
    public function getResultList() {
        $statement = $this->pdo->query($this->query);
        
        return $this->getResultListInternal($statement);
    }
    
    public function getSingeResult() {
        $statement = $this->pdo->query($this->query);
        
        if ($statement->columnCount() == 1) {
            return $statement->fetchColumn();
        } else {
            return $this->getResultListInternal($statement)[0];
        }
    }

    private function getResultListInternal(PDOStatement $statement) {
        
        // just needed for oneToOne-relations.
        $foreigns   = array();
        $resultList = array();
        
        $properties = $this->metaDataMap->getPropertiesByColumn($this->classname);
        $relations  = $this->metaDataMap->getRelations($this->classname);
        
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            
            $entity       = EntityFactory::create($this->classname);
            $primaryValue = null;
            
            foreach ($row as $column => $value) {
                
                if ($properties[$column]->isPrimary()) {
                    $primaryValue = $value;
                }
                
                // exclude oneToOne relations
                if (!$properties[$column]->hasRelation()) {
                    $properties[$column]->setValue($entity, $value);
                } else if ($properties[$column]->getRelation() instanceof OneToOne) {
                    $foreigns[$properties[$column]->getRelation()->getMappedBy()] = $value;
                }
            }
            
            
            foreach ($relations as $relation) {
                $table = $this->metaDataMap->getTableName($relation->getMappedBy());
                
                if ($relation instanceof OneToOne) {
                    $result = $this->handleOneToOne($entity, $relation, $table, $foreigns);
                } else if ($relation instanceof OneToMany) {
                    $result = $this->handleOneToMany($entity, $relation, $table, $primaryValue);
                } else if ($relation instanceof ManyToMany) {
                    $result = $this->handleManyToMany($entity, $relation, $table, $primaryValue);
                }
                
                $relation->getProperty()->setValue($entity, $result);
            }
            
            $resultList[] = $entity;
        }
        
        return $resultList;
    }
    
    private function handleOneToOne(Entity $entity, OneToOne $relation, $table, array $foreigns) {
        
        $primary = $this->metaDataMap->getPrimaryProperty($relation->getMappedBy());

        # TODO: Solve this somehow via prepared statements, to achieve better performance.
        # As this kind of query will called often on eager loading, it may be feasible.
        $query = "SELECT * FROM `{$table}` WHERE {$primary->getColumn()} = {$foreigns[$relation->getMappedBy()]}";
        $q = new TypedQuery($query, $relation->getMappedBy());
        
        if ($relation->isLazy()) {
            return new MockEntity($q, $entity, $relation->getProperty());
        } else {
            return $q->getSingeResult();
        }
    }
    
    private function handleOneToMany(Entity $entity, OneToMany $relation, $table, $primaryValue) {
        $x_column = $relation->getX_column();

        $query = "SELECT * FROM `{$table}` WHERE {$x_column} = {$primaryValue}";
        $q = new TypedQuery($query, $relation->getMappedBy());

        if ($relation->isLazy()) {
            return new MockEntityList($q, $entity, $relation->getProperty());
        } else {
            return $q->getResultList();
        }
    }
    
    private function handleManyToMany(Entity $entity, ManyToMany $relation, $table, $primaryValue) {
            $primary   = $this->metaDataMap->getPrimaryProperty($this->classname);

            $joinTable = $relation->getJoinTable();
            $column    = $relation->getColumn();
            $x_column  = $relation->getX_column();

            $query = "SELECT `{$table}`.* FROM `{$joinTable}` JOIN `{$table}` ON (`{$joinTable}`.{$x_column} = `{$table}`.{$primary->getColumn()}) WHERE `{$joinTable}`.{$column} = {$primaryValue}";
            $q = new TypedQuery($query, $relation->getMappedBy());

        if ($relation->isLazy()) {
            return new MockEntityList($q, $entity, $relation->getProperty());
        } else {
            return $q->getResultList();
        }
    }
    
}

?>
