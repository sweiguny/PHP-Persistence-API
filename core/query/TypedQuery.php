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
    
    /**
     * @var string
     */
    protected $classname;
    
    /**
     * @var EntityMetaDataMap
     */
    protected $metaDataMap;

    public function __construct($query, $fullyQualifiedClassname) {
        parent::__construct($query);
        
        # TODO: exclude also DDLs
        if ($this->type != "select") {
            throw new DomainException("Can only be a SELECT-statement.");
        }
        
        $this->classname   = trim($fullyQualifiedClassname);
        $this->metaDataMap = EntityMetaDataMap::getInstance();
    }
    
    /**
     * @return array A list of entities corresponding to the classname specified
     * in the constructor.
     */
    public function getResultList() {
        $statement = $this->conn->query($this->query);
        
        return $this->getResultListInternal($statement);
    }
    
    /**
     * @return Entity An entity corresponding to the classname specified in the
     * constructor.
     */
    public function getSingleResult() {
        $statement = $this->conn->query($this->query);
        
        return $this->getResultListInternal($statement)[0];
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
    
    # ==========================================================================
    # The following methods must be equal to the same in PreparedTypedQuery
    # ==========================================================================
    
    private function handleOneToOne(Entity $entity, OneToOne $relation, $table, array $foreigns) {
        $primary = $this->metaDataMap->getPrimaryProperty($relation->getMappedBy());

        $query  = "SELECT * FROM `{$table}` WHERE {$primary->getColumn()} = ?";
        $values = array($foreigns[$relation->getMappedBy()]);
        
        if ($relation->isLazy()) {
            return new MockEntity($query, $relation->getMappedBy(), $entity, $relation->getProperty(), $values);
        } else {
            $q = new PreparedTypedQuery($query, $relation->getMappedBy());
            return $q->getSingleResult($values);
        }
    }
    
    private function handleOneToMany(Entity $entity, OneToMany $relation, $table, $primaryValue) {
        $x_column = $relation->getX_column();

        $query  = "SELECT * FROM `{$table}` WHERE {$x_column} = ?";
        $values = array($primaryValue);

        if ($relation->isLazy()) {
            return new MockEntityList($query, $relation->getMappedBy(), $entity, $relation->getProperty(), $values);
        } else {
            $q = new PreparedTypedQuery($query, $relation->getMappedBy());
            return $q->getResultList($values);
        }
    }
    
    private function handleManyToMany(Entity $entity, ManyToMany $relation, $table, $primaryValue) {
        $primary   = $this->metaDataMap->getPrimaryProperty($this->classname);

        $joinTable = $relation->getJoinTable();
        $column    = $relation->getColumn();
        $x_column  = $relation->getX_column();

        $query  = "SELECT `{$table}`.* FROM `{$joinTable}` JOIN `{$table}` ON (`{$joinTable}`.{$x_column} = `{$table}`.{$primary->getColumn()}) WHERE `{$joinTable}`.{$column} = ?";
        $values = array($primaryValue);

        if ($relation->isLazy()) {
            return new MockEntityList($query, $relation->getMappedBy(), $entity, $relation->getProperty(), $values);
        } else {
            $q = new PreparedTypedQuery($query, $relation->getMappedBy());
            return $q->getResultList($values);
        }
    }
    
}

?>
