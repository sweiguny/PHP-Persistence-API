<?php

namespace PPA\tests\bootstrap;

use PHPUnit\Framework\TestCase;
use PPA\dbal\drivers\AbstractDriver;

abstract class AbstractDriverTestCase extends TestCase
{
    
    public abstract function getDriver(): AbstractDriver;
    
    public abstract function getDefaultPort(): int;
    public abstract function getDriverName(): string;
    public abstract function getCharset(): string;
    public abstract function getDefaultOptions(): array;
    
    public function testDriver()
    {
        $driver = $this->getDriver();
        
        $driverValues = [
            $driver->getDefaultPort(),
            $driver->getDriverName(),
            $driver->getCharset(),
            $driver->getDefaultOptions()
        ];
        
        $expectedValues = [
            $this->getDefaultPort(),
            $this->getDriverName(),
            $this->getCharset(),
            $this->getDefaultOptions()
        ];
        
        $this->assertEquals($expectedValues, $driverValues);
    }

}

?>