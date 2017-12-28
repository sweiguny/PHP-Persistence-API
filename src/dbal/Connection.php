<?php

namespace PPA\dbal;

use LogicException;
use PDO;
use PPA\dbal\drivers\AbstractDriver;
use PPA\dbal\event\ConnectionEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Connection
{
    /**
     *
     * @var AbstractDriver
     */
    private $driver;
    
    /**
     *
     * @var PDO
     */
    private $pdo;

    /**
     *
     * @var string
     */
    private $username;
    
    /**
     *
     * @var string
     */
    private $password;

    /**
     *
     * @var string
     */
    private $hostname;
    
    /**
     *
     * @var string
     */
    private $database;

    /**
     *
     * @var string
     */
    private $port;
    
    /**
     *
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(AbstractDriver $driver, EventDispatcherInterface $eventDispatcher, string $username, string $password, string $hostname, string $database, int $port = null)
    {
        $this->driver   = $driver;
        $this->username = $username;
        $this->password = $password;
        $this->hostname = $hostname;
        $this->database = $database;
        $this->port     = $port ?: $this->driver->getDefaultPort();
        
        $this->eventDispatcher = $eventDispatcher;
    }

    public function connect(): void
    {
        $dataSourceName  = $this->getDataSourceName();
        $connectionEvent = new ConnectionEvent($this->driver, $dataSourceName, $this->username);
        
        $this->eventDispatcher->dispatch(ConnectionEvent::PRE_CONNECT, $connectionEvent);
        
        $this->pdo = new PDO(
                $dataSourceName,
                $this->username,
                $this->password,
                $this->driver->getOptions()
            );
        
        $this->eventDispatcher->dispatch(ConnectionEvent::POST_CONNECT, $connectionEvent);
    }
    
    public function getDataSourceName(): string
    {
        return vsprintf("%s:host=%s;dbname=%s;charset=%s;port=%s", [
            $this->driver->getDriverName(),
            $this->hostname,
            $this->database,
            $this->driver->getCharset(),
            $this->port
        ]);
    }
    
    public function disconnect(): void
    {
        $this->pdo = null;
    }
    
    public function isConnected(): bool
    {
        return null != $this->pdo;
    }
    
    public function getPdo(): PDO
    {
        if ($this->pdo == null)
        {
            throw new LogicException('Connection must be established first. Do that by calling $connection->connect().');
        }
        
        return $this->pdo;
    }
    
    public function getDriver(): AbstractDriver
    {
        return $this->driver;
    }

    public function getHostname(): string
    {
        return $this->hostname;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function getPort(): int
    {
        return $this->port;
    }
    
}

?>
