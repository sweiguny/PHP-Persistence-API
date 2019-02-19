<?php

namespace PPA\tests\bootstrap\mock;

use PPA\dbal\drivers\AbstractDriver;

/**
 * Description of DummyDriver
 *
 * @author siwe
 */
class DriverMock extends AbstractDriver
{
    
    public function getCharset(): string
    {
        return "";
    }

    public function getDefaultOptions(): array
    {
        return [];
    }

    public function getDefaultPort(): int
    {
        return 0;
    }

    public function getDriverName(): string
    {
        return "";
    }

    public function getSystemIdentifier(): string
    {
        return "";
    }

    public function getValueIdentifier(): string
    {
        return "";
    }

}

?>
