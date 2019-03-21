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
        $offset          = 2; // Description, Parameters
        $index           = array_search(static::getDriver(), array_keys(DriverManager::DRIVER_MAP)) + 1;
        $expectedResults = ExpectedSQLResultsProvider::provideExpectedSQLResults();
        $data            = [];
        
        foreach ($expectedResults as $testCase => $results)
        {
            $parameters      = json_decode($results[$offset], true);
            $sql             = $results[$index + $offset];
            $data[$testCase] = [$sql, $parameters ?: []];
        }
        
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
    public function testExpectedSQLResults(string $sql, array $parameters)
    {
        if (empty($sql))
        {
            $this->markTestSkipped("This should only be, because the specific syntax is not available for the target DBMS.");
        }
        
        $errorHappend = false;
        $errorMessage = $sql . "\n" . print_r($parameters, true) . "\n";
//        echo "$sql\n";
        try
        {
            $stmt = self::$connection->getPdo()->prepare($sql);
            $stmt->execute($parameters);
        }
        catch (\PDOException $exception){
            $errorHappend  = true;
            $errorMessage .= $exception->getMessage();
        }
        
        $this->assertFalse($errorHappend, $errorMessage);
    }
    
}

?>
