<?php

namespace PPA\tests\bootstrap;

use PHPUnit\Framework\TestCase;
use PPA\core\EventDispatcher;
use PPA\core\exceptions\error\DriverNotInstalledError;
use PPA\core\exceptions\io\IOException;
use PPA\core\util\FileReader;
use PPA\dbal\Connection;
use PPA\dbal\TransactionManager;
use const PPA_TEST_BOOTSTRAP_PATH;

abstract class DatabaseTestCase extends TestCase
{
    /**
     * Has to be set in setUpBeforeClass() for each DBMS suite.
     * 
     * @var string
     */
    protected static $drivername;

    /**
     *
     * @var Connection
     */
    protected static $connection;
    
    /**
     *
     * @var TransactionManager
     */
    protected static $transactionManager;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        
        $eventDispatcher = new EventDispatcher();
        
        try
        {
            self::$connection = ConnectionProviderForTestEnvironment::getConnectionByName(self::$drivername);
            self::$connection->connect();

            self::$transactionManager = new TransactionManager(self::$connection, $eventDispatcher);

            self::setUpFixtures();
        }
        catch (DriverNotInstalledError $ex)
        {
            self::markTestSkipped("Skipping test class (" . static::class . ") for driver '" . self::$drivername . "'. Because: \"{$ex->getMessage()}\"");
        }
    }
    
    private static function setUpFixtures(): void
    {
//        echo "DatabaseTestCase::setUpFixtures\n";
        
        $addrStatePath = self::generateFilePathToFixtures("addr_country.csv");
        
        $filereader = new FileReader();
        $iterator   = $filereader->getLineIterator($addrStatePath);
        
        self::$transactionManager->begin();
        
        $header = explode(";", $iterator->current());
        $count  = count($header);
        
        $query = "INSERT INTO addr_country (id, name, short_name) VALUES(" . implode(",", array_fill(0, $count, "?")) . ")";
        $statement = self::$connection->getPdo()->prepare($query);
        
        for ($iterator->next(); $iterator->valid(); $iterator->next())
        {
            $line = $iterator->current();
            $values = explode(";", $line);
            
            $statement->execute($values);
        }
        
        self::$transactionManager->commit();
    }
    
    private static function generateFilePathToFixtures(string $filename): string
    {
        if (!file_exists($filepath = PPA_TEST_BOOTSTRAP_PATH . DIRECTORY_SEPARATOR . "fixtures" . DIRECTORY_SEPARATOR . $filename))
        {
            throw new IOException("File '{$filepath}' does not exist.");
        }
        
        return $filepath;
    }
    
    protected function setUp(): void
    {
//        print_r(DriverManager::getAvailableDrivers());
//        echo "DatabaseTestCase::setup\n";
        parent::setUp();
        
        self::$transactionManager->begin();
    }
    
    
    protected function tearDown(): void
    {
        parent::tearDown();
        
        self::$transactionManager->rollback();
    }
    
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        
//        echo __METHOD__."\n";
        
        self::$connection->getPdo()->query("DELETE FROM addr_country");
        self::$connection->disconnect();
    }
}

?>
