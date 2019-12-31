<?php

namespace PPA\orm;

use PPA\core\EventDispatcher;
use PPA\core\exceptions\runtime\db\TransactionException;
use PPA\dbal\ConnectionInterface;
use PPA\orm\event\transactions\TransactionBeginEvent;
use PPA\orm\event\transactions\TransactionCommitEvent;
use PPA\orm\event\transactions\TransactionRollbackEvent;

class TransactionManager
{
    /**
     *
     * @var ConnectionInterface
     */
    private $connection;

    /**
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct(ConnectionInterface $connection, EventDispatcher $eventDispatcher)
    {
        $this->connection      = $connection;
        $this->eventDispatcher = $eventDispatcher;
    }
    
    public function begin(): void
    {
        if ($this->inTransaction())
        {
            throw new TransactionException("Already in a transaction.");
        }
        
        $this->eventDispatcher->dispatch(TransactionBeginEvent::NAME, new TransactionBeginEvent());
        
        $this->connection->begin();
    }

    public function commit(): void
    {
        if (!$this->inTransaction())
        {
            throw new TransactionException("Not in a transaction.");
        }
        
        $this->eventDispatcher->dispatch(TransactionCommitEvent::NAME, new TransactionCommitEvent());
        
        $this->connection->commit();
    }

    public function rollback(): void
    {
        if (!$this->inTransaction())
        {
            throw new TransactionException("Not in a transaction.");
        }
        
        $this->eventDispatcher->dispatch(TransactionRollbackEvent::NAME, new TransactionRollbackEvent());
        
        $this->connection->rollBack();
    }
    
//    public function executeSql(string $query, array $parameters): \PPA\dbal\query\ResultSet
//    {
////        $this->connection->
//    }

    public function inTransaction(): bool
    {
        return $this->connection->inTransaction();
    }
    
    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
    
}

?>
