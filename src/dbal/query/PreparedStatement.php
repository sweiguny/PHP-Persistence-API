<?php

namespace PPA\dbal\query;

class PreparedStatement extends Statement
{
    /**
     *
     * @var \PDOStatement
     */
    private $pdoStatement = null;
    
    public function __construct(\PPA\dbal\Connection $connection, string $statement)
    {
        parent::__construct($connection, $statement);
        
        $this->pdoStatement = $this->pdo->prepare($statement);
    }
    
    public function execute(array $parameters = null): ResultSet
    {
        $this->pdoStatement->execute($parameters);
        
        return new ResultSet($this->pdoStatement);
    }
}

?>
