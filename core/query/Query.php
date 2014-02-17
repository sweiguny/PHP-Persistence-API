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
    protected $type;

    /**
     * @var PDO the connection
     */
    protected $pdo;

    public function __construct($query) {
        $this->pdo   = Bootstrap::getPDO();
        $this->query = trim($query);

        $firstWord  = explode(" ", $this->query);
        $this->type = strtolower($firstWord[0]);
    }

    /**
     * Depending on the $full_qualified_classname parameter, a list of objects
     * is returned.
     * 
     * @param string $full_qualified_classname The full qualified classname.
     * @return array A list of objects.
     */
    public function getResultList() {
        if ($this->type == "select") {
            return $this->getResultListInternal($this->pdo->query($this->query));
        } else {
            return $this->getSingleResult();
        }
    }

    /**
     * 
     * @param string $full_qualified_classname
     * @return object|scalar
     */
    public function getSingleResult() {
        $statement = $this->pdo->query($this->query); # TODO: Prepared statement

        switch ($this->type) {
            case 'select': {
                    if ($statement->columnCount() == 1) {
                        $result = $statement->fetchColumn();
                    } else {
                        $result = $this->getResultListInternal($statement)[0];
                    }
                    break;
                }
            case 'update':
            case 'delete':
                $result = $statement->rowCount();
                break;
            case 'insert':
                $result = $this->pdo->lastInsertId();
                break;
            default:
                break;
        }

        return $result;
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