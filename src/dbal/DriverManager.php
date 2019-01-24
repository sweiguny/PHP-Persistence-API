<?php

namespace PPA\dbal;

use PPA\core\EventDispatcher;
use PPA\dbal\drivers\AbstractDriver;

class DriverManager
{
    /**
     *
     * @var array
     */
    private static $driverMap = [
         "mysql" => '\PPA\dbal\drivers\concrete\MySQLDriver',
         "pgsql" => '\PPA\dbal\drivers\concrete\PgSQLDriver'
    ];
    
    public static function getConnection(
            EventDispatcher $eventDispatcher,
            string $driverName,
            array  $driverOptions,
            string $username, string $password,
            string $hostname, string $database, int $port = null
    ): Connection
    {
        $driver = self::createDriver(self::lookupDriver($driverName), $driverOptions);
        
        return new Connection($driver, $eventDispatcher, $username, $password, $hostname, $database, $port);
    }
    
    private static function createDriver(string $driverClass, array $driverOptions): AbstractDriver
    {
        return new $driverClass($driverOptions);
    }
    
    protected static function lookupDriver(string $driverName): string
    {
        return self::$driverMap[strtolower($driverName)];
    }
    
}

?>
