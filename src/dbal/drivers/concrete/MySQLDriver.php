<?php

namespace PPA\dbal\drivers\concrete;

use PPA\dbal\drivers\AbstractDriver;

class MySQLDriver extends AbstractDriver
{
    
    public function getCharset(): string
    {
        return "utf8"; // Change to utf8mb4?
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

    public function getValueIdentifier(): string
    {
        return "\"";
    }

    public function getSystemIdentifier(): string
    {
        return "`";
    }
    
}

?>
