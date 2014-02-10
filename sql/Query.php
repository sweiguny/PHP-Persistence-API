<?php

namespace PPA\sql;

use PDO;
use PPA\Bootstrap;

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
        
        if ($full_qualified_classname == null) {
            return $statement->fetchAll(PDO::FETCH_OBJ);
        } else {
            throw new Exception("Not yet implemented");
        }
    }
    
    /**
     * 
     * @param type $full_qualified_classname
     * @return object
     */
    public function getSingeResult($full_qualified_classname = null) {
        return $this->getResultList($full_qualified_classname)[0];
    }
}

?>
