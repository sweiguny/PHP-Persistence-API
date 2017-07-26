<?php

namespace PPA\dbal\drivers\concrete;

class MySQLDriver extends \PPA\dbal\drivers\AbstractDriver
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

}

?>
