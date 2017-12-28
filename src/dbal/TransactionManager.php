<?php

namespace PPA\dbal;

use PDO;
use PPA\orm\event\transactions\TransactionBeginEvent;
use PPA\orm\event\transactions\TransactionCommitEvent;
use PPA\orm\event\transactions\TransactionRollbackEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

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

    /**
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public function __construct(Connection $connection, EventDispatcher $eventDispatcher)
    {
        $this->connection      = $connection;
        $this->pdo             = $connection->getPdo();
        $this->eventDispatcher = $eventDispatcher;
    }
    
    public function begin(): void
    {
        if ($this->inTransaction())
        {
            throw new TransactionException("Already in a transaction.");
        }
        
        $this->eventDispatcher->dispatch(TransactionBeginEvent::NAME, new TransactionBeginEvent());
        
        $this->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        if (!$this->inTransaction())
        {
            throw new TransactionException("Not in a transaction.");
        }
        
        $this->eventDispatcher->dispatch(TransactionCommitEvent::NAME, new TransactionCommitEvent());
        
        $this->pdo->commit();
        $this->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
    }

    public function rollback(): void
    {
        if (!$this->inTransaction())
        {
            throw new TransactionException("Not in a transaction.");
        }
        
        $this->eventDispatcher->dispatch(TransactionRollbackEvent::NAME, new TransactionRollbackEvent());
        
        $this->pdo->rollBack();
        $this->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
    }

    public function inTransaction(): bool
    {
        return (bool) $this->pdo->inTransaction();
    }
    
    public function getConnection(): Connection
    {
        return $this->connection;
    }
    
}

?>
