<?php

namespace PPA\core\query;

use PDO;
use PDOStatement;
use PPA\PPA;

class PreparedQuery implements iPreparedQuery {
    
    /**
     * @var string the query
     */
    protected $query;
    protected $type;

    /**
     * @var PDO the connection
     */
    protected $conn;
    
    /**
     * @var PDOStatement
     */
    protected $statement;

    public function __construct($query) {
        $this->conn  = PPA::getInstance()->getConnection();
        $this->query = trim($query);

        $firstWord  = explode(" ", $this->query);
        $this->type = strtolower($firstWord[0]);
        
        
        // To discern, if constructor was called by
        // class-inheritor or the class itself
        if (get_class() == get_class($this)) {
            PPA::log(3000, "Preparing query: {$this->query}");
        } else {
            PPA::log(5000, "Preparing query for class '" . $this->classname . "': {$this->query}");
        }
        
        
        $this->statement = $this->conn->prepare($this->query);
    }

    public function getResultList(array $values) {
        if ($this->type == "select") {
            PPA::log(3501, "Executing query for resultlist with values: " . print_r($values, true));
            $this->statement->execute($values);
            
            $result = $this->getResultListInternal();
            PPA::log(3510, "Retrieved " . count($result) . " rows");
            return $result;
        } else {
            return $this->getSingleResult($values);
        }
    }

    public function getSingleResult(array $values) {
        PPA::log(3001, "Executing query for single result with values: " . print_r($values, true));
        $this->statement->execute($values);

        switch ($this->type) {
            case 'select': {
                    if ($this->statement->columnCount() == 1) {
                        $result = $this->statement->fetchColumn();
                        PPA::log(3015, "Retrieved scalar: {$result}");
                    } else {
                        $result = $this->getResultListInternal()[0];
                        PPA::log(3010, "Retrieved one row");
                    }
                    break;
                }
            case 'update':
            case 'delete':
                $result = $this->statement->rowCount();
                PPA::log(3020, "{$result} rows affected");
                break;
            case 'insert':
                $result = $this->conn->lastInsertId();
                PPA::log(3030, "Last inserted primary key: {$result}");
                break;
            default:
                break;
        }

        return $result;
    }

    private function getResultListInternal() {
        return $this->statement->fetchAll(PDO::FETCH_OBJ);
    }
    
}

?>