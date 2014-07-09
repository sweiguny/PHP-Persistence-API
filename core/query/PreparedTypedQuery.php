<?php

namespace PPA\core\query;

use DomainException;
use PDO;
use PPA\core\Entity;
use PPA\core\EntityFactory;
use PPA\core\EntityMetaDataMap;
use PPA\core\mock\MockEntity;
use PPA\core\mock\MockEntityList;
use PPA\core\relation\ManyToMany;
use PPA\core\relation\OneToMany;
use PPA\core\relation\OneToOne;
use PPA\PPA;

class PreparedTypedQuery extends PreparedQuery {
    
    protected $classname;
    protected $metaDataMap;

    public function __construct($query, $fullyQualifiedClassname) {
        $this->classname   = trim($fullyQualifiedClassname);
        $this->metaDataMap = EntityMetaDataMap::getInstance();
        
        parent::__construct($query);
        
        if ($this->type != "select") {
            throw new DomainException("Can only be a SELECT-statement.");
        }
    }
    
    public function getSingleResult(array $values) {
        PPA::log(5001, "Executing query for single result for class '" . $this->classname . "': {$this->query}");
        $this->statement->execute($values);
        
        $result = $this->getResultListInternal()[0];
        PPA::log(5010, "Retrieved one Entity ('\\" . get_class($result) . "') " . $result->getShortInfo());
        return $result;
    }
    
    public function getResultList(array $values) {
        PPA::log(5501, "Executing query for resultlist for class '" . $this->classname . "': {$this->query}");
        $this->statement->execute($values);
        
        $result = $this->getResultListInternal();
        PPA::log(5510, "Retrieved " . count($result) . " Entities");
        return $result;
    }

    private function getResultListInternal() {
        
        // just needed for oneToOne-relations.
        $foreigns   = array();
        $resultList = array();
        
        $properties = $this->metaDataMap->getPropertiesByColumn($this->classname);
        $relations  = $this->metaDataMap->getRelations($this->classname);
        
        while ($row = $this->statement->fetch(PDO::FETCH_ASSOC)) {
            
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
    # The following methods must be similar to the same in TypedQuery
    # ==========================================================================
    
    private function handleOneToOne(Entity $entity, OneToOne $relation, $table, array $foreigns) {
        $primary = $this->metaDataMap->getPrimaryProperty($relation->getMappedBy());

        $query  = "SELECT * FROM `{$table}` WHERE {$primary->getColumn()} = ?";
        $values = array($foreigns[$relation->getMappedBy()]);
        
        if ($relation->isLazy()) {
            PPA::log(1001, "Lazy OneToOne-Relation - MockEntity will be created");
            return new MockEntity($query, $relation->getMappedBy(), $entity, $relation->getProperty(), $values);
        } else {
            PPA::log(1002, "Eager OneToOne-Relation - Query will be created");
            $q = new PreparedTypedQuery($query, $relation->getMappedBy());
            return $q->getSingleResult($values);
        }
    }
    
    private function handleOneToMany(Entity $entity, OneToMany $relation, $table, $primaryValue) {
        $x_column = $relation->getX_column();

        $query  = "SELECT * FROM `{$table}` WHERE {$x_column} = ?";
        $values = array($primaryValue);

        if ($relation->isLazy()) {
            PPA::log(1003, "Lazy OneToMany-Relation - MockEntityList will be created");
            return new MockEntityList($query, $relation->getMappedBy(), $entity, $relation->getProperty(), $values);
        } else {
            PPA::log(1004, "Eager OneToMany-Relation - Query will be created");
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
            PPA::log(1005, "Lazy ManyToMany-Relation - MockEntityList will be created");
            return new MockEntityList($query, $relation->getMappedBy(), $entity, $relation->getProperty(), $values);
        } else {
            PPA::log(1006, "Eager ManyToMany-Relation - Query will be created");
            $q = new PreparedTypedQuery($query, $relation->getMappedBy());
            return $q->getResultList($values);
        }
    }
    
}

?>
