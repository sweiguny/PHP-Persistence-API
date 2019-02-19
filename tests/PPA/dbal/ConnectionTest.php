<?php

namespace PPA\tests\dbal;

use PHPUnit\Framework\TestCase;
use PPA\dbal\Connection;
use PPA\tests\bootstrap\ConnectionProviderForTestEnvironment;

/**
 * @coversDefaultClass PPA\dbal\Connection
 */
class ConnectionTest extends TestCase
{
    public function provideConnections(): array
    {
        return ConnectionProviderForTestEnvironment::getConnections();
    }
    
    /**
     * @covers ::isConnected
     * 
     * @dataProvider provideConnections
     */
    public function testIsConnected(Connection $connection): void
    {
        $this->assertFalse($connection->isConnected(), "Should be false, since no connection attempt was done yet.");
        
        $connection->connect();
        
        $this->assertTrue($connection->isConnected(), "Should be true, since we already tried to connect.");
    }

}

?>
