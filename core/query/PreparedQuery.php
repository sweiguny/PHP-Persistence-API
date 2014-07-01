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
        
        $this->statement = $this->conn->prepare($this->query);
    }

    public function getResultList(array $values) {
        $this->statement->execute($values);
        
        if ($this->type == "select") {
            return $this->getResultListInternal();
        } else {
            return $this->getSingleResult($values);
        }
    }

    public function getSingleResult(array $values) {
        $this->statement->execute($values);

        switch ($this->type) {
            case 'select': {
                    if ($this->statement->columnCount() == 1) {
                        $result = $this->statement->fetchColumn();
                    } else {
                        $result = $this->getResultListInternal()[0];
                    }
                    break;
                }
            case 'update':
            case 'delete':
                $result = $this->statement->rowCount();
                break;
            case 'insert':
                $result = $this->conn->lastInsertId();
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