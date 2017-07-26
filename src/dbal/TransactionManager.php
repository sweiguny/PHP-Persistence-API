<?php

namespace PPA\dbal;

use PDO;

class TransactionManager
{
    /**
     *
     * @var Connection
     */
    private $connection;
    
    /**
     *
     * @var PDO
     */
    private $pdo;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->pdo        = $connection->getPdo();
    }
    
    public function begin()
    {
        if ($this->inTransaction())
        {
            throw new TransactionException("Already in a transaction.");
        }
        
        $this->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
        $this->pdo->beginTransaction();
    }

    public function commit()
    {
        if (!$this->inTransaction())
        {
            throw new TransactionException("Not in a transaction.");
        }
        
        $this->pdo->commit();
        $this->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
    }

    public function rollback()
    {
        if (!$this->inTransaction())
        {
            throw new TransactionException("Not in a transaction.");
        }
        
        $this->pdo->rollBack();
        $this->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
    }

    public function inTransaction(): bool
    {
        return (bool) $this->pdo->inTransaction();
    }
    
}

?>
