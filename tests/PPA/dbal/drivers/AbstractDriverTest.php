<?php

namespace PPA\tests\dbal\drivers;

use PDO;
use PHPUnit\Framework\TestCase;
use PPA\dbal\drivers\AbstractDriver;
use PPA\tests\bootstrap\mock\DriverMock;

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
        $testOptions = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];
        
        $driver = new DriverMock($testOptions);

        $expectedOptions = AbstractDriver::DEFAULT_OPTIONS + $driver->getDefaultOptions() + $testOptions;
        
        $this->assertEquals($expectedOptions, $driver->getOptions());
    }
    
}

?>