<?php

namespace PPA\tests\bootstrap;

use PPA\dbal\Connection;
use PPA\dbal\DriverManager;

class TestDriverManager extends DriverManager
{
    public static function getConnectionByGlobals(array $driverOptions = []): Connection
    {
        $driverName = $GLOBALS["driver"];
        $username   = $GLOBALS["username"];
        $password   = $GLOBALS["password"];
        $database   = $GLOBALS["database"];
        $hostname   = $GLOBALS["hostname"];
        $port       = isset($GLOBALS["port"]) ?: null;
        
        return self::getConnection($driverName, $driverOptions, $username, $password, $hostname, $database, $port);
    }

}

?>
