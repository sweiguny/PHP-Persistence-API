<?php

namespace PPA\dbal;

use PDO;
use PPA\core\EventDispatcher;
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
    private $host;
    
    /**
     *
     * @var string
     */
    private $dbname;

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

    public function __construct(AbstractDriver $driver, EventDispatcherInterface $eventDispatcher, string $username, string $password, string $host, string $dbname, int $port = null)
    {
        $this->driver   = $driver;
        $this->username = $username;
        $this->password = $password;
        $this->host     = $host;
        $this->dbname   = $dbname;
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
            $this->host,
            $this->dbname,
            $this->driver->getCharset(),
            $this->port
        ]);
    }
    
    public function isConnected(): bool
    {
        return null != $this->pdo;
    }
    
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

}

?>
