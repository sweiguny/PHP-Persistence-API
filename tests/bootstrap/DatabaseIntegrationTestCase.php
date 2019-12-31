<?php

namespace PPA\tests\bootstrap;

use PDO;
use PDOException;
use PPA\dbal\DriverManager;
use PPA\dbal\query\builder\QueryBuilder;
use PPA\tests\bootstrap\DatabaseTestCase;
use function PPA\dbal\query\builder\AST\catalogObjects\Field;
use function PPA\dbal\query\builder\AST\catalogObjects\Table;
use function PPA\dbal\query\builder\AST\expressions\Parameter;
use function PPA\dbal\query\builder\AST\operators\GreaterEquals;
use function PPA\dbal\query\builder\AST\operators\LowerEquals;

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
        
        $errorHappened = false;
        $errorMessage  = $sql . "\n" . print_r($parameters, true) . "\n";
//        echo "$sql\n";
        try
        {
            $stmt = self::$connection->getPdo()->prepare($sql);
            $stmt->execute($parameters);
        }
        catch (PDOException $exception){
            $errorHappened  = true;
            $errorMessage  .= $exception->getMessage();
        }
        
        $this->assertFalse($errorHappened, $errorMessage);
    }
    
    # PDO-specific tests
    
//    public function testLittleResultRetrievalAndIteration()
//    {
//    }
    
    public function testHugeResultRetrievalAndIteration()
    {
        $mem = memory_get_usage();
        $pdo = self::$connection->getPdo();
        
        $this->queryBuilder->select()->from(Table("addr_city"));
        
        $stmt = $pdo->query($this->queryBuilder->sql());
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        
        // Data is fetched to have memory usage.
        while ($result = $stmt->fetch());
        
        $mem = round((memory_get_usage() - $mem) / 1024 / 1024, 2);
        $this->assertLessThanOrEqual(0.05, $mem, "Memory usage too high (in M).");
    }
    
    /**
     * Cannot execute queries while other unbuffered queries are active.
     */
    public function testConcurrentStatements()
    {
        $this->expectException(\PDOException::class);
        $this->expectExceptionCode("HY000");
        $this->expectExceptionMessageRegExp("#^SQLSTATE\[HY000\]: General error: 2014#m");
        
        $pdo = self::$connection->getPdo();
        
        $this->queryBuilder->select()->from(Table("addr_country"))->where()->criteria(LowerEquals(Field("id"), Parameter()));
        $query1 = $this->queryBuilder->sql();
        
        $this->queryBuilder->clear();
        
        $this->queryBuilder->select()->from(Table("addr_country"))->where()->criteria(GreaterEquals(Field("id"), Parameter()));
        $query2 = $this->queryBuilder->sql();
        
        $stmt1 = $pdo->prepare($query1);
        $stmt2 = $pdo->prepare($query2);
        
        $stmt1->execute([4]);
        $stmt2->execute([5]);
    }
    
    /**
     * Multiple execution
     */
    public function testPreparedStatements()
    {
        $pdo = self::$connection->getPdo();
        
        $this->queryBuilder->select(\PPA\dbal\query\builder\AST\expressions\functions\aggregate\Max(Field("id")))->from(Table("addr_country"));
        $query = $this->queryBuilder->sql();
        $maxId = $pdo->query($query)->fetchColumn();
        
        $this->assertEquals(6, $maxId);
        $this->queryBuilder->clear();
        
        $this->queryBuilder->select()->from(Table("addr_country"))->where()->criteria(\PPA\dbal\query\builder\AST\operators\Equals(Field("id"), Parameter()));
        $query = $this->queryBuilder->sql();
        
        $stmt = $pdo->prepare($query);
        
        $executions = 0;
        for ($i = 1; $i <= $maxId; $i++)
        {
            $stmt->execute([$i]);
            $stmt->fetch();
            $executions++;
        }
        
        $this->assertEquals($maxId, $executions);
    }
}

?>
