<?php

namespace PPA\sql;

use PDO;
use PPA\Bootstrap;
use ReflectionClass;

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
     * 
     * @param string $full_qualified_classname The full qualified classname.
     * @return array
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
    
    private function getResultListInternal($statement, $full_qualified_classname = null) {
        if ($full_qualified_classname == null) {
            return $statement->fetchAll(PDO::FETCH_OBJ);
        } else {
            $resultList = array();
            $reflection = new ReflectionClass($full_qualified_classname);
            $properties = $reflection->getProperties();
            
            foreach ($properties as $prop) {
                $prop->setAccessible(true);
            }
            
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $$full_qualified_classname = $reflection->newInstanceWithoutConstructor();
                
                
                \PPA\prettyDump($properties);
                foreach ($properties as $prop) {
                    $doccomment = explode("*", substr($prop->getDocComment(), 3, -2));
                    $propName   = $prop->getName();
//                    \PPA\prettyDump($doccomment);
                    
                    foreach ($doccomment as $doc) {
                        $pattern = "#@column.*#i";
                        preg_match($pattern, $doc, $matches);
                        
                        if (!empty($matches)) {
//                            \PPA\prettyDump($matches);
                            $pattern = "#\(.*name[\s]*=[\s]*[\"\'](.*)[\"\']\)#i";
                            preg_match($pattern, $matches[0], $matches);
//                            \PPA\prettyDump($matches);
                            if (isset($matches[1])) {
                                $propName = $matches[1];
                            }
                        }
                    }
                    
                    
                    if (isset($row[$propName])) {
                        $prop->setValue($$full_qualified_classname, $row[$propName]);
                    }
                }
                
                $resultList[] = $$full_qualified_classname;
            }
            
            return $resultList;
        }
    }

}

?>
