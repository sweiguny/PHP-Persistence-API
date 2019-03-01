<?php

namespace PPA\tests\bootstrap;

use PPA\core\EventDispatcher;
use PPA\core\exceptions\error\DriverNotInstalledError;
use PPA\core\exceptions\error\DriverNotSupportedError;
use PPA\core\exceptions\io\IOException;
use PPA\dbal\Connection;
use PPA\dbal\DriverManager;
use const PPA_TEST_CONFIG_PATH;

class ConnectionProviderForTestEnvironment extends DriverManager
{
    private static $collection = [];
    private static $connections = [];
    private static $exceptions  = [];

    public static function init(): void
    {
        $drivernames = array_map("strtolower", explode(",", $GLOBALS["drivers"])); // Ensure, that driver names are in lower case.

        foreach ($drivernames as $drivername)
        {
            self::$collection[$drivername] = require_once self::generateDriverParametersFilePath($drivername);

            $parameters = self::$collection[$drivername];
            $username   = $parameters["username"];
            $password   = $parameters["password"];
            $database   = $parameters["database"];
            $hostname   = $parameters["hostname"];
            $port       = isset($parameters["port"]) ?: null;

            try
            {
                $connection = self::createConnection(new EventDispatcher(), $drivername, [], $username, $password, $hostname, $database, $port);
                self::$connections[$drivername] = [$connection];
            }
            catch (DriverNotInstalledError $ex)
            {
                /*
                 * Deferred Exception.
                 * Thrown in self.:getConnectionByName(),
                 * which is called by DatabaseTestCase::setUpBeforeClass()
                 * called by a certain integration test, to be able to skip
                 * that integration test, if the driver is not available.
                 */
                self::$exceptions[$drivername] = $ex;
            }
        }
    }
    
    private static function generateDriverParametersFilePath(string $drivername): string
    {
        if (!file_exists($filepath = PPA_TEST_CONFIG_PATH . DIRECTORY_SEPARATOR . $drivername . ".php"))
        {
            throw new IOException("There is no config for driver '{$drivername}'. You have either forgotten to create one or you have a typo in the driver name.");
        }
        
        return $filepath;
    }
    
    /**
     * 
     * @param string $drivername
     * @return Connection
     * @throws DriverNotInstalledError
     */
    public static function getConnectionByName(string $drivername): Connection
    {
        if (isset(self::$exceptions[$drivername]))
        {
            throw self::$exceptions[$drivername];
        }
        
        if (!self::driverAvailable($drivername))
        {
            throw new DriverNotSupportedError("Driver '{$drivername}' is highly probably wrongly defined (typo?) in your integration test, because DriverManager wasn't complaining. So the driver list configured is valid. Use the constants of DriverManager to prevent typos.");
        }
        
        return self::$connections[$drivername][0];
    }
    
    public static function getConnections(): array
    {
        return self::$connections;
    }
    
    public static function driverAvailable(string $drivername): bool
    {
        return isset(self::$connections[$drivername]);
    }
    
}

?>
