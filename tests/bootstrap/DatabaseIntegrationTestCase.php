<?php

namespace PPA\tests\bootstrap;

use PPA\dbal\DriverManager;
use PPA\dbal\query\builder\QueryBuilder;
use PPA\tests\bootstrap\DatabaseTestCase;

abstract class DatabaseIntegrationTestCase extends DatabaseTestCase
{
    /**
     *
     * @var QueryBuilder
     */
    protected $queryBuilder;
    
    public static function setUpBeforeClass(): void
    {
        self::$drivername = static::getDriver();
        
        parent::setUpBeforeClass();
    }

    public function setUp(): void
    {
        parent::setUp();
        
        $this->queryBuilder = new QueryBuilder(self::$connection->getDriver());
    }
    
    public function tearDown(): void
    {
        parent::tearDown();
        
        $this->queryBuilder->clear();
    }
    
    abstract static protected function getDriver(): string;
    
    /**
     * Provides the expected SQL-Statements for a certain driver.
     * 
     * @return array
     */
    public function provideExpectedSQLResults(): array
    {
        $index = array_search(static::getDriver(), array_keys(DriverManager::DRIVER_MAP)) + 1;
        $expectedResults = ExpectedSQLResultsProvider::provideExpectedSQLResults();
        
        $data = ["mytest" => ["SELECT * FROM `addr_country` WHERE `id` = ?"]];
        
        foreach ($expectedResults as $testCase => $results)
        {
            $data[$testCase] = [$results[$index + 1]];
        }
        
//        print_r($data);
//        var_dump($data);
//        echo $data;
        
        return $data;
    }
    
    /**
     * This method tests whether the expected SQL-Statements are accepted by the
     * corresponding DBMS. Otherwise testing against them doesn't make much sense.
     * 
     * @dataProvider provideExpectedSQLResults
     * 
     * @param string $sql
     */
    public function testExpectedSQLResults(string $sql)
    {
        $exception = null;
        
        try
        {
            self::$connection->getPdo()->prepare($sql);
        }
        catch (\PDOException $exception){}
        
        $this->assertNull($exception, $exception->getMessage());
    }
    
}

?>
