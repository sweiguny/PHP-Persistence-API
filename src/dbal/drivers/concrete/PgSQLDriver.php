<?php

namespace PPA\dbal\drivers\concrete;

use PPA\dbal\drivers\AbstractDriver;

class PgSQLDriver extends AbstractDriver
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
        return 5432;
    }

    public function getDriverName(): string
    {
        return "pgsql";
    }

    public function getValueIdentifier(): string
    {
        return "'";
    }

    public function getSystemIdentifier(): string
    {
        return "\"";
    }

}

?>
