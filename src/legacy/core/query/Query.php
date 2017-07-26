<?php

namespace PPA\core\query;

use PDO;
use PDOStatement;
use PPA\PPA;

class Query implements iQuery
{
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
     * 
     * 
     * @param string $query
     */
    public function __construct($query)
    {
        $this->conn  = PPA::getInstance()->getConnection();
        $this->query = trim($query);

        $firstWord  = explode(" ", $this->query);
        $this->type = strtolower($firstWord[0]);
    }

    /**
     * The query is executed.
     * 
     * If the query string is a select, then a list of POPOs is returned.
     * 
     * If the query string is not a select, this function behaves like
     * <b>getSingleResult()</b>.
     * 
     * @return array A list of POPOs.
     */
    public function getResultList()
    {
        if ($this->type == "select")
        {
            PPA::log(2500, [$this->query]);
            $result = $this->getResultListInternal($this->conn->query($this->query));

            PPA::log(2510, [count($result)]);
            
            return $result;
        }
        else
        {
            return $this->getSingleResult();
        }
    }

    /**
     * The query is executed.
     * This method has some return values, depending on the query and the result
     * set.
     * 
     * On <b>select</b> the first value of the result set is returned as an POPO.
     * If there is just one column in the result set, only the value of this
     * column is returnd.
     * 
     * On <b>insert</b> the last inserted primary value is returned.
     * 
     * On <b>update</b> or <b>delete</b> the number of affected rows is returned.
     * 
     * @return mixed
     */
    public function getSingleResult()
    {
        PPA::log(2000, [$this->query]);
        
        $statement = $this->conn->query($this->query);
        $result    = null;

        switch ($this->type)
        {
            case 'select':
                {
                    if ($statement->columnCount() == 1)
                    {
                        $result = $statement->fetchColumn();
                        PPA::log(2015, [$result]);
                    }
                    else
                    {
                        $result = $this->getResultListInternal($statement);
                        
                        if (empty($result))
                        {
                            $result = null;
                        }
                        else
                        {
                            $result = $result[0];
                        }
                        
                        PPA::log(2010);
                    }
                    break;
                }
            case 'update':
            case 'delete':
                $result = (int) $statement->rowCount();
                PPA::log(2020, [$result]);
                break;
            case 'insert':
                $result = $this->conn->lastInsertId();
                PPA::log(2030, [$result]);
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
    private function getResultListInternal(PDOStatement $statement)
    {
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

}

?>