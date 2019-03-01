<?php

namespace PPA\dbal;

use PDO;
use PPA\core\EventDispatcher;
use PPA\core\exceptions\ExceptionFactory;
use PPA\dbal\drivers\AbstractDriver;

class DriverManager
{
    const PGSQL = "pgsql";
    const MYSQL = "mysql";
    
    /**
     *
     * @var array
     */    
    const DRIVER_MAP = [
        self::MYSQL => '\PPA\dbal\drivers\concrete\MySQLDriver',
        self::PGSQL => '\PPA\dbal\drivers\concrete\PgSQLDriver'
    ];
    
    public static function createConnection(
            EventDispatcher $eventDispatcher,
            string $driverName,
            array  $driverOptions,
            string $username, string $password,
            string $hostname, string $database, int $port = null
    ): Connection
    {
        $driver = self::createDriver(self::lookupDriver(strtolower($driverName)), $driverOptions);
        
        return new Connection($driver, $eventDispatcher, $username, $password, $hostname, $database, $port);
    }
    
    private static function createDriver(string $driverClass, array $driverOptions): AbstractDriver
    {
        return new $driverClass($driverOptions);
    }
    
    protected static function lookupDriver(string $driverName): string
    {
        if (!isset(self::DRIVER_MAP[$driverName]))
        {
            throw ExceptionFactory::DriverNotSupported($driverName, array_keys(self::DRIVER_MAP));
        }
        
        if (!in_array($driverName, PDO::getAvailableDrivers()))
        {
            throw ExceptionFactory::DriverNotInstalled($driverName, PDO::getAvailableDrivers());
        }
        
        return self::DRIVER_MAP[$driverName];
    }
    
    /**
     * 
     * @return array (string)$key => Drivername,
     *               (bool)$value => true means Driver is provided by both PDO & PPA,
     *                               false means Driver is only provided by PPA.
     */
    public static function getAvailableDrivers(): array
    {
        $driverNames = array_keys(self::DRIVER_MAP);
        $pdoDrivers  = PDO::getAvailableDrivers();
        
        $supportedBoth    = array_intersect($driverNames, $pdoDrivers);
        $supportedPPAonly = array_diff($driverNames, $pdoDrivers);
        
        return array_merge(
                array_combine($supportedBoth,    array_fill(0, count($supportedBoth),    true)),
                array_combine($supportedPPAonly, array_fill(0, count($supportedPPAonly), false))
            );
    }
    
}

?>
