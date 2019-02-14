<?php

namespace PPA\dbal\query;

use PPA\dbal\Connection;

/**
 * Description of Query
 *
 * @author siwe
 */
class Query
{
    /**
     *
     * @var Connection
     */
    private $connection;
    

    private $pdo;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
//        \PDO::fetch_l
        $this->pdo = $connection->getPdo();
    }

    public function execute(string $query)
    {
        
        
    }
    
}

?>
