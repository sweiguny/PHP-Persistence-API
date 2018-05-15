<?php

namespace PPA\tests\bootstrap;

use PPA\dbal\drivers\AbstractDriver;

/**
 * Description of DummyDriver
 *
 * @author siwe
 */
class DummyDriver extends AbstractDriver
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

}

?>
