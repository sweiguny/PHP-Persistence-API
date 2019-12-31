<?php

namespace PPA\dbal\query;

class ResultSet
{
    /**
     *
     * @var \PDOStatement
     */
    private $pdoStatement = null;
    
    public function __construct(\PDOStatement $pdoStatement)
    {
        $this->pdoStatement = $pdoStatement;
    }

    public function getSingleResult(string $classname): ?object
    {
        return $this->pdoStatement->fetchObject($classname);
    }
    
    public function getResultList(string $classname): ?array
    {
        
    }
}

?>
