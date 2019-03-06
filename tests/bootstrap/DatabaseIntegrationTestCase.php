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
     * @dataProvider provideExpectedSQLResults
     * 
     * @param string $index
     */
    public function testExpectedSQLResults(string $sql)
    {
//        $expectedResults = ExpectedSQLResultsProvider::provideExpectedSQLResults();
//        
//        $sql = $expectedResults[$index + 1];
        
        $stmt = self::$connection->getPdo()->prepare($sql);
//        $stmt->execute();
        print_r($stmt->errorInfo());
//        print_r(self::$connection->getPdo()->errorInfo());
        
    }
    
}

?>
