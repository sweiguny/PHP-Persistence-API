<?php

namespace PPA\tests\dbal\drivers;

use PPA\dbal\drivers\AbstractDriver;
use PPA\dbal\drivers\concrete\MySQLDriver;
use PPA\tests\bootstrap\AbstractDriverTestCase;

/**
 * @coversDefaultClass \PPA\dbal\drivers\concrete\MySQLDriver
 */
class MySQLDriverTest extends AbstractDriverTestCase
{
    /**
     * @covers ::getCharset
     * 
     * @return string
     */
    public function getCharset(): string
    {
        return "utf8";
    }

    /**
     * @covers ::getDefaultOptions
     * 
     * @return array
     */
    public function getDefaultOptions(): array
    {
        return [];
    }

    /**
     * @covers ::getDefaultPort
     * 
     * @return int
     */
    public function getDefaultPort(): int
    {
        return 3306;
    }

    /**
     * @covers ::getDriverName
     * 
     * @return string
     */
    public function getDriverName(): string
    {
        return "mysql";
    }

    /**
     * 
     * @return AbstractDriver
     */
    public function getDriver(): AbstractDriver
    {
        return new MySQLDriver();
    }

}

?>
