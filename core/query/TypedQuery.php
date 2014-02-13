<?php

namespace PPA\core\query;

use DomainException;
use PDO;
use PDOStatement;
use PPA\core\EntityFactory;
use PPA\core\EntityMetaDataMap;

class TypedQuery extends Query {

    protected $classname;
    protected $metaDataMap;

    public function __construct($query, $fullyQualifiedClassname) {
        $firstWord = explode(" ", trim($query));
        $firstWord = strtolower($firstWord[0]);
        
        # TODO: exclude also DDLs
        if (in_array($firstWord, array("update", "insert", "delete"))) {
            throw new DomainException("Cannot be an UPDATE- or INSERT- or DELETE-statement.");
        }
        
        parent::__construct($query);
        
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
        $resultList = array();
        $properties = $this->metaDataMap->getPropertiesByColumn($this->classname);
        
//        $relations  = array();
        $relations  = $this->metaDataMap->getRelations($this->classname);
        $oneToOnes = array();
//        \PPA\prettyDump($properties);
//        \PPA\prettyDump($propertiesByName);
//        \PPA\prettyDump($relations);
        
        
//        die("here");
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            
            $entity = EntityFactory::create($this->classname);
            $primaryValue = null;
            
            foreach ($row as $column => $value) {
                
                if ($properties[$column]->isPrimary()) {
                    $primaryValue = $value;
                }
                
                // exclude oneToOne relations
                if (!$properties[$column]->hasRelation()) {
                    $properties[$column]->setValue($entity, $value);
                }
                else if ($properties[$column]->getRelation()->isOneToOne()) {
                    $oneToOnes[$properties[$column]->getRelation()->getMappedBy()] = $value;
                }
            }
            
            
            foreach ($relations as $relation) {
                if ($relation->isOneToOne()) {
//                    if ($relation->isLazy()) {
//                        $properties[$key]->setValue($$full_qualified_classname, new MockEntity($relation->getMappedBy(), $value, $$full_qualified_classname, $properties[$key]));
//                    } else {
                        $primary = $this->metaDataMap->getPrimaryProperty($relation->getMappedBy());
                        $table   = $this->metaDataMap->getTableName($relation->getMappedBy());
                        
                        
                        # TODO: Solve this somehow via prepared statements, to achieve better performance.
                        # As this kind of query will called often on eager loading, it may be feasible.
                        $query = "SELECT * FROM `{$table}` WHERE {$primary->getColumn()} = {$oneToOnes[$relation->getMappedBy()]}";
//                        echo $query;
                        $q = new TypedQuery($query, $relation->getMappedBy());
//                        \PPA\prettyDump($q);

                        $relation->getProperty()->setValue($entity, $q->getSingeResult());
//                        $properties[$primary->getColumn()]->setValue($entity, $q->getSingeResult());
//                    }
                } else if ($relation->isOneToMany()) {
                    
                    $primary  = $this->metaDataMap->getPrimaryProperty($relation->getMappedBy());

                    $table   = $this->metaDataMap->getTableName($relation->getMappedBy());
                    
                    $x_column = $relation->getJoinTable()["x_column"];
                    
//                    select * from orderpos where order_id = 3;
//                    \PPA\prettyDump($relation);
//                    var_dump($x_column);
                    $query = "SELECT * FROM `{$table}` WHERE {$x_column} = {$primaryValue}";
                    echo $query;
                    $q = new TypedQuery($query, $relation->getMappedBy());
//                    \PPA\prettyDump($q->getResultList());
                    $relation->getProperty()->setValue($entity, $q->getResultList());
                    
                    
                } else if ($relation->isManyToMany()) {
                    $primary  = $this->metaDataMap->getPrimaryProperty($this->classname);

                    $table   = $this->metaDataMap->getTableName($relation->getMappedBy());
                    $joinTable = $relation->getJoinTable()["name"];
                    $column = $relation->getJoinTable()["column"];
                    $x_column = $relation->getJoinTable()["x_column"];
                    
                    $query = "SELECT `{$table}`.* FROM `{$joinTable}` JOIN `{$table}` ON (`{$joinTable}`.{$x_column} = `{$table}`.{$primary->getColumn()}) WHERE `{$joinTable}`.{$column} = {$primaryValue}";
//                    echo $query;
                    $q = new TypedQuery($query, $relation->getMappedBy());
//                    \PPA\prettyDump($q->getResultList());
                    $relation->getProperty()->setValue($entity, $q->getResultList());
//                    $propertiesByName[$primary->getColumn()]->setValue($entity, $q->getSingeResult());
                }
            }
            
            $resultList[] = $entity;
        }
        
        return $resultList;
    }
    
}

?>
