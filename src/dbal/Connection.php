<?php

namespace PPA\dbal;

use PDO;
use PPA\dbal\drivers\AbstractDriver;
use PPA\dbal\events\ConnectionEvent;
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

    public function connect()
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
    
    public function disconnect()
    {
        $this->pdo = null;
    }
    
    public function isConnected(): bool
    {
        return null != $this->pdo;
    }
    
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function getHostname()
    {
        return $this->hostname;
    }

    public function getDatabase()
    {
        return $this->database;
    }

    public function getPort()
    {
        return $this->port;
    }
    
}

?>
