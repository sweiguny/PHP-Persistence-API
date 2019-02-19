<?php

namespace PPA\tests\bootstrap;

use PPA\core\EventDispatcher;
use PPA\dbal\Connection;
use PPA\dbal\DriverManager;

class ConnectionProviderForTestEnvironment extends DriverManager
{
    private static $collection = [];
    private static $connections = [];

    public static function init(): void
    {
        $drivernames = explode(",", $GLOBALS["drivers"]);

        foreach ($drivernames as $drivername)
        {
            self::$collection[$drivername] = require_once PPA_TEST_CONFIG_PATH . DIRECTORY_SEPARATOR . $drivername . ".php";
            
            
            $parameters = self::$collection[$drivername];
            $username   = $parameters["username"];
            $password   = $parameters["password"];
            $database   = $parameters["database"];
            $hostname   = $parameters["hostname"];
            $port       = isset($parameters["port"]) ?: null;
            
            self::$connections[$drivername] = [self::createConnection(new EventDispatcher(), $drivername, [], $username, $password, $hostname, $database, $port)];
        }
    }
    
    public static function getConnections(): array
    {
        return self::$connections;
    }
    
    public static function getConnectionByName(string $drivername): Connection
    {
        return self::$connections[$drivername][0];
    }
    
}

?>
