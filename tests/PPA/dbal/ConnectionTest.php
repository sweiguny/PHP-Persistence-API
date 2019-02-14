<?php

namespace PPA\tests\dbal;

use PHPUnit\Framework\TestCase;
use PPA\core\EventDispatcher;
use PPA\dbal\Connection;
use PPA\dbal\DriverManager;

/**
 * @coversDefaultClass PPA\dbal\Connection
 */
class ConnectionTest extends TestCase
{
    /**
     *
     * @var Connection
     */
    private $connection;


    protected function setUp(): void
    {
        if ($this->connection == null)
        {
            $driverName = $GLOBALS["driver"];
            $username   = $GLOBALS["username"];
            $password   = $GLOBALS["password"];
            $hostname   = $GLOBALS["hostname"];
            $database   = $GLOBALS["database"];
            $port       = isset($GLOBALS["port"]) ?: null;

            $this->connection = DriverManager::getConnection(new EventDispatcher(), $driverName, [], $username, $password, $hostname, $database, $port);
        }
    }
    
    /**
     * @covers ::isConnected
     */
    public function testIsConnected(): void
    {
        $connection = $this->connection;
        
        $this->assertFalse($connection->isConnected());
        
        $connection->connect();
        
        $this->assertTrue($connection->isConnected());
    }

}

?>
