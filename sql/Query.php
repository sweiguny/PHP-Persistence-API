<?php

namespace PPA\sql;

use PDO;
use PDOStatement;
use PPA\Bootstrap;
use PPA\EntityAnalyzer;
use PPA\EntityFactory;

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
        $statement = $this->_pdo->query($this->_query);

        return $this->getResultListInternal($statement, $full_qualified_classname);
    }

    /**
     * 
     * @param string $full_qualified_classname
     * @return object|scalar
     */
    public function getSingeResult($full_qualified_classname = null) {
        $statement = $this->_pdo->query($this->_query);
        
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
        if ($full_qualified_classname == null) {
            return $statement->fetchAll(PDO::FETCH_OBJ);
        } else {
            $resultList = array();
            $analyzer   = new EntityAnalyzer($full_qualified_classname);
            $properties = $analyzer->getPersistenceProperties();
            
            foreach ($properties as $property) {
                $property->setAccessible(true);
            }
            
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $$full_qualified_classname = EntityFactory::create($full_qualified_classname);
                
                foreach ($row as $key => $value) {
                    
                    if (isset($properties[$key])) {
                        $properties[$key]->setValue($$full_qualified_classname, $value);
                    }
                }
                
                $resultList[] = $$full_qualified_classname;
            }
            
            return $resultList;
        }
    }

}

?>
