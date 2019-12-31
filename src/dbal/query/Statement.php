<?php

namespace PPA\dbal\query;

use PDO;
use PPA\dbal\Connection;

class Statement implements StatementInterface
{
    /**
     *
     * @var Connection
     */
    protected $connection;
    
    /**
     *
     * @var PDO
     */
    protected $pdo;

    /**
     *
     * @var string
     */
    private $statement;
    
    public function __construct(Connection $connection, string $statement)
    {
        $this->statement  = $statement;
        $this->connection = $connection;
        $this->pdo        = $connection->getPdo();
    }

    public function execute(): ResultSet
    {
        return new ResultSet($this->pdo->query($this->statement));
    }
    
}

?>
