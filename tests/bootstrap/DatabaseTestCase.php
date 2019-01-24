<?php

namespace PPA\tests\bootstrap;

use PHPUnit\DbUnit\Database\DefaultConnection;
use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\Framework\TestCase;
use PPA\core\EventDispatcher;
use PPA\dbal\Connection;
use PPA\dbal\DriverManager;

abstract class DatabaseTestCase extends TestCase
{
    use TestCaseTrait;
    
    /**
     *
     * @var Connection
     */
    protected static $connection;
    
    /**
     *
     * @var DefaultConnection 
     */
    private $defaultConnection;

    protected function getConnection(): DefaultConnection
    {
        if (self::$connection == null)
        {
            $driverName = $GLOBALS["driver"];
            $username   = $GLOBALS["username"];
            $password   = $GLOBALS["password"];
            $database   = $GLOBALS["database"];
            $hostname   = $GLOBALS["hostname"];
            $port       = isset($GLOBALS["port"]) ?: null;

            self::$connection = DriverManager::getConnection(new EventDispatcher(), $driverName, [], $username, $password, $hostname, $database, $port);
        }
        
        if ($this->defaultConnection == null)
        {
            $this->defaultConnection = $this->createDefaultDBConnection(self::$connection->getPdo(), self::$connection->getDatabase());
        }
        
        return $this->defaultConnection;
    }

}

?>
