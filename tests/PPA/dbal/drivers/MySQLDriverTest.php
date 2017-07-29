<?php

namespace PPA\tests\dbal\drivers;

use PDO;
use PHPUnit\Framework\TestCase;
use PPA\dbal\drivers\concrete\MySQLDriver;

/**
 * @coversDefaultClass \PPA\dbal\drivers\concrete\MySQLDriver
 */
class MySQLDriverTest extends TestCase
{
    
    /**
     * 
     * @covers ::getDefaultPort
     * @covers ::getDriverName
     * @covers ::getCharset
     * @covers ::getDefaultOptions
     */
    public function testDriver()
    {
        $driver = new MySQLDriver();
        
        $this->assertEquals(3306,    $driver->getDefaultPort());
        $this->assertEquals("mysql", $driver->getDriverName());
        $this->assertEquals("utf8",  $driver->getCharset());
        $this->assertEquals([],      $driver->getDefaultOptions());
    }
    
    /**
     * @depends testDriver
     * 
     * @covers \PPA\dbal\drivers\AbstractDriver::__construct
     * @covers \PPA\dbal\drivers\AbstractDriver::getOptions
     */
    public function testOptions()
    {
        $abstractDefaultOptions = [
            PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_AUTOCOMMIT => true
        ];
        $testOptions = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];
        
        $driver = new MySQLDriver($testOptions);
        
        $expectedOptions = array_merge($abstractDefaultOptions, $driver->getDefaultOptions(), $testOptions);
        
        $this->assertEquals($expectedOptions, $driver->getOptions());
    }
    
}

?>
