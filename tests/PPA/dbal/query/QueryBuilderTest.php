<?php

namespace PPA\tests\dbal;

use Generator;
use PHPUnit\Framework\TestCase;
use PPA\core\exceptions\io\IOException;
use PPA\dbal\drivers\concrete\MySQLDriver;
use PPA\dbal\query\builder\AST\expressions\properties\Field;
use PPA\dbal\query\builder\AST\expressions\properties\FieldDistinct;
use PPA\dbal\query\builder\AST\expressions\properties\FieldSUM;
use PPA\dbal\query\builder\QueryBuilder;

/**
 * @coversDefaultClass \PPA\dbal\query\builder\QueryBuilder
 */
class QueryBuilderTest extends TestCase
{
    /**
     * Data of expected.csv file indexed by name of the test cases, w/o header.
     * 
     * @var array
     */
    protected static $expectedCsvData = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        
        if (!file_exists($filepath = PPA_BOOTSTRAP_PATH . DIRECTORY_SEPARATOR . "expected.csv"))
        {
            throw new IOException("File '{$filepath}' does not exist.");
        }
        
        $iterator = self::readExpectedCSv($filepath);
        $index    = 0;
        
        foreach ($iterator as $iteration)
        {
            if ($index++ > 0) // skip header
            {
                $temp = explode(";", $iteration);
                self::$expectedCsvData[array_shift($temp)] = $temp;
            }
        }
    }
    
    private static function readExpectedCSv(string $filepath): Generator
    {
        $handle = fopen($filepath, "r");
        
        while (!feof($handle))
        {
            yield trim(fgets($handle));
        }

        fclose($handle);
    }
    
    public function provideQueryBuilder(): array
    {
        return [
            "mysql" => [1, new QueryBuilder(new MySQLDriver())]
        ];
    }
    
    private function checkResult(string $testCase, int $index, string $sql): void
    {
        $offset   = 1;
        $expected = self::$expectedCsvData;
        
        if (!isset($expected[$testCase]))
        {
            throw new \Exception("Test case '{$testCase}' not defined in expected.csv. If you are sure, the test case is defined, please check the delimiter of the test file. It should be ';'.");
        }
        
        $this->assertEquals($expected[$testCase][$offset + $index], $sql);
    }
    
    /**
     * @covers ::select
     * @group singleton
     * @dataProvider provideQueryBuilder
     */
    public function testSelect(int $index, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->select()->fromTable("test", "c");
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }
    
    /**
     * @covers ::select
     * 
     * @dataProvider provideQueryBuilder
     */
    public function testJoinClause(int $index, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->select()->fromTable("customer", "c")
                ->join("order", "o")
                ;
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }
    
    /**
     * @covers ::select
     * 
     * @dataProvider provideQueryBuilder
     */
    public function testSimpleJoinOnClause(int $index, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->select()->fromTable("customer", "c")
                ->join("order", "o")->on()
                    ->withField("id", "c")->equalsField("customer", "o")
                    ->andWithParameter("name")->equalsLiteral(10)
                ;
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }
    
    /**
     * @covers ::select
     * 
     * @dataProvider provideQueryBuilder
     */
    public function testOnClauseWithGroup(int $index, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->select()->fromTable("customer", "c")
                ->join("order", "o")->on()
                    ->withField("id", "c")->equalsField("customer", "o")
                    ->andGroup()
                        ->withField("id", "o")->greaterLiteral(100)
                        ->andWithField("id", "o")->lowerLiteral(1000)
                    ->closeGroup()
                    ->orGroup()
                        ->withField("id", "c")->betweenLiteral(1)->andLiteral(10)
                        ->andWithField("id", "o")->betweenLiteral(0)->andLiteral(100)
                    ->closeGroup()
                ;
        
//        var_dump($queryBuilder->sql());
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }
    
    /**
     * @covers ::select
     * 
     * @dataProvider provideQueryBuilder
     */
    public function testSimpleWhereClause(int $index, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->select()->fromTable("customer")
                ->where()
                    ->withField("id")->equalsField("old_identifier")
                ;
        
//        var_dump($queryBuilder->sql());
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }
    
    /**
     * @covers ::select
     * 
     * @dataProvider provideQueryBuilder
     */
    public function testComplexWhereClause(int $index, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->select()->fromTable("customer", "c")
                ->where()
                    ->withField("target_group", "c")->equalsLiteral(10)
                    ->andGroup()
                        ->withField("name")->equalsLiteral("jochen")
                    ->closeGroup()
                    ->orGroup()
                        ->withParameter()->betweenLiteral(1)->andLiteral(2)
                    ->closeGroup()
                ;
        
//        var_dump($queryBuilder->sql());
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }
    
    /**
     * @covers ::select
     * 
     * @dataProvider provideQueryBuilder
     */
    public function testInClause(int $index, QueryBuilder $queryBuilder): void
    {
        $subQB = clone $queryBuilder;
        $subQuery = $subQB->select(new FieldDistinct("age"));
        $subQuery->fromTable("order");
        
        $queryBuilder->select()->fromTable("customer", "c")
                ->where()
                    ->withField("id", "c")->inLiterals([10,20,30])
                    ->andWithField("age")->inSubquery($subQuery)
                ;
        
//        var_dump($queryBuilder->sql());
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }
    
    /**
     * @covers ::select
     * 
     * @dataProvider provideQueryBuilder
     */
    public function testJoinWithGroupByAndAggregateFunctionAndHavingClause(int $index, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->select(new FieldSUM("age"), new Field("type"))->fromTable("customer")
                ->join("order")->on()->withField("id", "customer")->equalsField("customer_id", "order")
                ->where()
                    ->withField("id")->inLiterals([10,20,30])
                    ->andWithField("id2")->inLiterals([30,20,10])
                ->groupBy(new Field("type"))
                ->having()
                    ->withField("test")->greaterEqualsLiteral(10)
                ;
        
//        var_dump($queryBuilder->sql());
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }
    
    /**
     * @covers ::update
     * 
     * @dataProvider provideQueryBuilder
     */
    public function testUpdateWithWhereClause(int $index, QueryBuilder $queryBuilder): void
    {
//        $queryBuilder->update()->table("customer")->set(["a", "?"], ["b", 10]);
        $queryBuilder->update()->table("customer")
                ->set("name")->toParameter()
                ->set("zip")->toParameter()
                ->where()
                    ->withField("id")->equalsLiteral(1)
                ;
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }
    
    /**
     * @covers ::delete
     * 
     * @dataProvider provideQueryBuilder
     */
    public function testDeleteWithWhereClause(int $index, QueryBuilder $queryBuilder): void
    {
        
//        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }

}

?>
