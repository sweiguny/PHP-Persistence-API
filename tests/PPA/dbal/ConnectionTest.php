<?php

namespace PPA\tests\dbal;

use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\Framework\TestCase;
use PPA\dbal\Connection;
use PPA\dbal\DriverManager;

class ConnectionTest extends TestCase
{
    /**
     *
     * @var Connection
     */
    private $connection;


    protected function setUp()
    {
        if ($this->connection == null)
        {
            $driverName = $GLOBALS["driver"];
            $username   = $GLOBALS["username"];
            $password   = $GLOBALS["password"];
            $database   = $GLOBALS["database"];
            $hostname   = $GLOBALS["hostname"];
            $port       = isset($GLOBALS["port"]) ?: null;

            $this->connection = DriverManager::getConnection($driverName, [], $username, $password, $hostname, $database, $port);
        }
    }
    
    public function testConnected()
    {
        $connection = $this->connection;
        
        $this->assertFalse($connection->isConnected());
        
        $connection->connect();
        
        $this->assertTrue($connection->isConnected());
    }
    

    protected function getDataSet(): IDataSet
    {
        
    }

}

?>
