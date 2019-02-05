<?php

namespace PPA\dbal\drivers\concrete;

use PPA\dbal\drivers\AbstractDriver;

class MySQLDriver extends AbstractDriver
{
    
    public function getCharset(): string
    {
        return "utf8";
    }

    public function getDefaultOptions(): array
    {
        return [];
    }

    public function getDefaultPort(): int
    {
        return 3306;
    }

    public function getDriverName(): string
    {
        return "mysql";
    }

    public function getCloseIdentifier(): string
    {
        return $this->getOpenIdentifier();
    }

    public function getOpenIdentifier(): string
    {
        return "`";
    }

}

?>
