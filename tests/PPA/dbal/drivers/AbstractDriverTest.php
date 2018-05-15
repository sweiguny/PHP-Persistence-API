<?php

namespace PPA\tests\dbal\drivers;

use PDO;
use PHPUnit\Framework\TestCase;
use PPA\tests\bootstrap\DummyDriver;

/**
 * @coversDefaultClass PPA\dbal\drivers\AbstractDriver
 */
class AbstractDriverTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getOptions
     */
    public function testOptions(): void
    {
        $abstractDefaultOptions = [
            PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_AUTOCOMMIT => true
        ];
        $testOptions = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];
        
        $driver = new DummyDriver($testOptions);

        $expectedOptions = array_merge($abstractDefaultOptions, $driver->getDefaultOptions(), $testOptions);
        
        $this->assertEquals($expectedOptions, $driver->getOptions());
    }
    
}

?>