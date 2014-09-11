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
            PPA::log(3000, array($this->query));
        } else {
            PPA::log(5000, array($this->classname, $this->query));
        }
        
        
        $this->statement = $this->conn->prepare($this->query);
    }

    public function getResultList(array $values) {
        if ($this->type == "select") {
            PPA::log(3501, array(print_r($values, true)));
            $this->statement->execute($values);
            
            $result = $this->getResultListInternal();
            PPA::log(3510, array(count($result)));
            return $result;
        } else {
            return $this->getSingleResult($values);
        }
    }

    public function getSingleResult(array $values) {
        PPA::log(3001, array(print_r($values, true)));
        $this->statement->execute($values);

        switch ($this->type) {
            case 'select': {
                    if ($this->statement->columnCount() == 1) {
                        $result = $this->statement->fetchColumn();
                        PPA::log(3015, array($result));
                    } else {
                        $result = $this->getResultListInternal();
                        if (empty($result)) {
                            $result = null;
                        } else {
                            $result = $result[0];
                        }
                        PPA::log(3010);
                    }
                    break;
                }
            case 'update':
            case 'delete':
                $result = (int)$this->statement->rowCount();
                PPA::log(3020, array($result));
                break;
            case 'insert':
                $result = $this->conn->lastInsertId();
                PPA::log(3030, array($result));
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