<?php

namespace PPA\sql;

use PDO;
use PDOStatement;
use PPA\Bootstrap;
use PPA\EntityAnalyzer;
use PPA\EntityFactory;
use PPA\EntityMap;

/**
 * @copyright copyright (c) by Simon Weiguny <s.weiguny@gmail.com>
 * @author Simon Weiguny - 10.02.2014
 */
class Query {

    /**
     *
     * @var string the query
     */
    private $_query;

    /**
     * @var PDO the connection
     */
    private $_pdo;

    public function __construct($query) {
        $this->_pdo   = Bootstrap::getPDO();
        $this->_query = $query;
    }

    /**
     * Depending on the $full_qualified_classname parameter, a list of objects
     * is returned.
     * 
     * @param string $full_qualified_classname The full qualified classname.
     * @return array A list of objects.
     */
    public function getResultList($full_qualified_classname = null) {
        $statement = $this->_pdo->query($this->_query); # TODO: Prepared statement

        return $this->getResultListInternal($statement, $full_qualified_classname);
    }

    /**
     * 
     * @param string $full_qualified_classname
     * @return object|scalar
     */
    public function getSingeResult($full_qualified_classname = null) {
        $statement = $this->_pdo->query($this->_query); # TODO: Prepared statement
        
        if ($statement->columnCount() == 1 && $full_qualified_classname == null) {
            return $statement->fetchColumn();
        } else {
            return $this->getResultListInternal($statement, $full_qualified_classname)[0];
        }
    }
    
    /**
     * Returns a list of objects. The kind of object depends on the $full_qualified_classname
     * parameter.
     * 
     * @param PDOStatement $statement
     * @param string $full_qualified_classname
     * @return array The result list.
     */
    private function getResultListInternal($statement, $full_qualified_classname = null) {
        
        // Check if a classname to be returned is set.
        if ($full_qualified_classname == null) {
            
            // Return a Plain Old Php Object (POPO). ;)
            return $statement->fetchAll(PDO::FETCH_OBJ);
        } else {
            $resultList = array();
            $properties = EntityMap::getInstance()->getPropertiesByColumn($full_qualified_classname);
//            \PPA\prettyDump($properties);
            
            // Fetch the row of the database as an associative array.
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                
                // Instanciate an object of the given classname.
                $$full_qualified_classname = EntityFactory::create($full_qualified_classname);
//                echo "here";
                
//                echo "here";
                
                // Iterate through the columns and set the properties of the object.
                foreach ($row as $key => $value) {
                    if (isset($properties[$key])) {
                        
                        if ($properties[$key]->hasRelation()) {
                            $relation = $properties[$key]->getRelation();
                            if ($relation->isOneToOne()) {
                                if ($relation->isLazy()) {
throw new \Exception();
                                } else {
                                    // must know id of $relation->mappedby
//                                    var_dump($relation);
                                    $id = EntityMap::getInstance()->getPrimaryProperty($relation->getMappedBy());
                                    $table = EntityMap::getInstance()->getTableName($relation->getMappedBy());
//                                    \PPA\prettyDump($id);
//                                    \PPA\prettyDump($table);
                                    
                                    $query = "SELECT * FROM `{$table}` WHERE {$id->getColumn()} = {$value}";
                                    $q = new Query($query);
                                    
                                    $properties[$key]->setValue($$full_qualified_classname, $q->getSingeResult($relation->getMappedBy()));
                                }
                            }
                        } else {
                            // Standard
                            $properties[$key]->setValue($$full_qualified_classname, $value);
                        }
                    }
                }
                
                $resultList[] = $$full_qualified_classname;
            }
            
            return $resultList;
        }
    }

}

?>
