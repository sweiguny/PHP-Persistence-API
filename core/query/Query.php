<?php

namespace PPA\core\query;

use PDO;
use PDOStatement;
use PPA\Bootstrap;

class Query implements iQuery {

    /**
     *
     * @var string the query
     */
    protected $query;

    /**
     * @var PDO the connection
     */
    protected $pdo;

    public function __construct($query) {
        $this->pdo   = Bootstrap::getPDO();
        $this->query = trim($query);
    }

    /**
     * Depending on the $full_qualified_classname parameter, a list of objects
     * is returned.
     * 
     * @param string $full_qualified_classname The full qualified classname.
     * @return array A list of objects.
     */
    public function getResultList() {
        $statement = $this->pdo->query($this->query);
        
        return $this->getResultListInternal($statement);
    }

    /**
     * 
     * @param string $full_qualified_classname
     * @return object|scalar
     */
    public function getSingeResult() {
        $statement = $this->pdo->query($this->query); # TODO: Prepared statement
        
        if ($statement->columnCount() == 1) {
            return $statement->fetchColumn();
        } else {
            return $this->getResultListInternal($statement)[0];
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
    private function getResultListInternal(PDOStatement $statement) {
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

}

?>