<?php

namespace PPA\dbal;

use LogicException;
use PDO;
use PDOException;
use PPA\core\exceptions\ExceptionFactory;
use PPA\dbal\drivers\AbstractDriver;
use PPA\dbal\event\ConnectionEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Connection implements ConnectionInterface
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
        
        try
        {
            $this->pdo = new PDO(
                    $dataSourceName,
                    $this->username,
                    $this->password,
                    $this->driver->getOptions()
                );
        }
        catch (PDOException $exc) // Otherwise PDO exposes user credentials.
        {
            $this->catchConnectionException($exc, $connectionEvent);
        }
        
        $this->eventDispatcher->dispatch(ConnectionEvent::POST_CONNECT, $connectionEvent);
    }
    
    private function catchConnectionException(PDOException $exc, ConnectionEvent $connectionEvent)
    {
        $this->eventDispatcher->dispatch(ConnectionEvent::CONNECTION_ERROR, $connectionEvent);
        
        $message = $exc->getMessage();
        $connectionEvent->setMessage($message);

        throw ExceptionFactory::Connection("[" . ConnectionEvent::CONNECTION_ERROR . "|{$connectionEvent->getDriver()}] {$message}");
    }

    public function getDataSourceName(): string
    {
        return vsprintf("%s:host=%s;dbname=%s;client_encoding=%s;port=%s", [
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
