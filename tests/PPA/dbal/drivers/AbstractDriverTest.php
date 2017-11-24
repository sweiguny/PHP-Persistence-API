<?php

namespace PPA\tests\dbal\drivers;

use PDO;
use PHPUnit\Framework\TestCase;
use PPA\dbal\drivers\AbstractDriver;

/**
 * @coversDefaultClass PPA\dbal\drivers\AbstractDriver
 */
class AbstractDriverTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getOptions
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
        
        $driver = new class($testOptions) extends AbstractDriver {

            public function getCharset(): string
            {
                
            }

            public function getDefaultOptions(): array
            {
                return [];
            }

            public function getDefaultPort(): int
            {
                
            }

            public function getDriverName(): string
            {
                
            }
        };

        $expectedOptions = array_merge($abstractDefaultOptions, $driver->getDefaultOptions(), $testOptions);
        
        $this->assertEquals($expectedOptions, $driver->getOptions());
    }
    
}

?>