<?php

namespace PPA\tests\bootstrap;

use PHPUnit\Framework\TestCase;
use PPA\core\EventDispatcher;
use PPA\core\exceptions\error\DriverNotInstalledError;
use PPA\dbal\Connection;
use PPA\dbal\TransactionManager;
use PPA\orm\EntityAnalyser;
use PPA\tests\bootstrap\entity\Address;
use PPA\tests\bootstrap\entity\City;
use PPA\tests\bootstrap\entity\Country;
use PPA\tests\bootstrap\entity\District;
use PPA\tests\bootstrap\entity\State;
use PPA\tests\bootstrap\entity\Street;

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
    
    /**
     *
     * @var EntityAnalyser
     */
    protected static $entityAnalyser;
    
    /**
     *
     * @var FixtureSetup
     */
    private static $fixtureSetup;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        
        $eventDispatcher = new EventDispatcher();
        
        try
        {
            self::$connection = ConnectionProviderForTestEnvironment::getConnectionByName(self::$drivername);
            self::$connection->connect();

            self::$transactionManager = new TransactionManager(self::$connection, $eventDispatcher);
            self::$entityAnalyser     = new EntityAnalyser();
            self::$fixtureSetup       = new FixtureSetup(self::$connection, self::$entityAnalyser, self::$transactionManager);
            
            self::setUpFixtures(self::$fixtureSetup);
        }
        catch (DriverNotInstalledError $ex)
        {
            self::markTestSkipped("Skipping test class (" . static::class . ") for driver '" . self::$drivername . "'. Because: \"{$ex->getMessage()}\"");
        }
    }
    
    /**
     * Please respect foreign keys...
     * 
     * @param FixtureSetup $setup
     * @return void
     */
    private static function setUpFixtures(FixtureSetup $setup): void
    {
        try
        {
            $setup->setUpFixtures(Country::class);
            $setup->setUpFixtures(State::class);
            $setup->setUpFixtures(District::class);
            $setup->setUpFixtures(City::class);
            $setup->setUpFixtures(Street::class);
            $setup->setUpFixtures(Address::class);
        }
        catch (\Exception $ex)
        {
            if (self::$connection->getPdo()->inTransaction())
            {
                self::$connection->getPdo()->rollBack();
            }
            
            self::$fixtureSetup->tearDownFixtures();
            self::markTestIncomplete($ex->getMessage() . "\n" . $ex->getPrevious()->getMessage());
        }
    }
    
    protected function setUp(): void
    {
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
        
        self::$fixtureSetup->tearDownFixtures();
        
        self::$connection->disconnect();
    }
}

?>
