<?php

namespace PPA\tests\dbal;

use Generator;
use PHPUnit\Framework\TestCase;
use PPA\core\exceptions\io\IOException;
use PPA\dbal\drivers\concrete\MySQLDriver;
use PPA\dbal\query\builder\QueryBuilder;
use const PPA_BOOTSTRAP_PATH;
use function PPA\dbal\query\builder\AST\catalogObjects\Field;
use function PPA\dbal\query\builder\AST\catalogObjects\Table;
use function PPA\dbal\query\builder\AST\expressions\functions\aggregate\Sum;
use function PPA\dbal\query\builder\AST\expressions\Literal;
use function PPA\dbal\query\builder\AST\expressions\Parameter;
use function PPA\dbal\query\builder\AST\operators\Alias;
use function PPA\dbal\query\builder\AST\operators\AsteriskWildcard;
use function PPA\dbal\query\builder\AST\operators\Between;
use function PPA\dbal\query\builder\AST\operators\Equals;
use function PPA\dbal\query\builder\AST\operators\Greater;
use function PPA\dbal\query\builder\AST\operators\GreaterEquals;
use function PPA\dbal\query\builder\AST\operators\InSubquery;
use function PPA\dbal\query\builder\AST\operators\InValues;
use function PPA\dbal\query\builder\AST\operators\Lower;
use function PPA\dbal\query\builder\AST\operators\NullValue;

/**
 * @coversDefaultClass \PPA\dbal\query\builder\QueryBuilder
 */
class QueryBuilderTestNew extends TestCase
{
    /**
     * Data of expected.csv file indexed by name of the test cases, w/o header.
     * 
     * @var array
     */
    protected static $expectedResults = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        
        $filepath = self::createFilePathToExpectedResultsFile("expected.csv");
        $iterator = self::readExpectedResultsFile($filepath);
        $index    = 0;
        
        foreach ($iterator as $iteration)
        {
            if ($index++ > 0) // skip header
            {
                $temp = explode(";", $iteration);
                self::$expectedResults[array_shift($temp)] = $temp;
            }
        }
    }
    
    private static function createFilePathToExpectedResultsFile(string $filename): string
    {
        if (!file_exists($filepath = PPA_BOOTSTRAP_PATH . DIRECTORY_SEPARATOR . $filename))
        {
            throw new IOException("File '{$filepath}' does not exist.");
        }
        
        return $filepath;
    }
    
    private static function readExpectedResultsFile(string $filepath): Generator
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
        // TODO: When PHP provides covariance, refactor QueryBuilder :)
        return [
            "mysql" => [1, new QueryBuilder(new MySQLDriver())]
        ];
    }
    
    private function checkResult(string $testCase, int $index, string $sql): void
    {
//        $offset   = 1;
//        $expected = self::$expectedResults;
        
        if (!isset(self::$expectedResults[$testCase]))
        {
            throw new \Exception("Test case '{$testCase}' not defined in expected.csv. If you are sure, the test case is defined, please check the delimiter of the test file. It should be ';'.");
        }
        
        $expected = self::$expectedResults[$testCase][$index + 1];
//        echo "<pre>" . print_r($sql, true)."</pre>";
//        echo "<pre>" . print_r($expected[$testCase][$offset + $index], true)."</pre>";
        
//        echo $testCase . "\n";
//        echo $sql . "\n";
//        echo $expected . "\n\n\n";
        
        $this->assertEquals($expected, $sql);
    }
    
    /**
     * @covers ::select
     * @group singleton
     * @dataProvider provideQueryBuilder
     */
    public function testSelectAll(int $index, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->select()->from(Alias(Table("test"), "c"));
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }
    
    /**
     * @covers ::select
     * @group singleton
     * @dataProvider provideQueryBuilder
     */
    public function testSelectAllWithAsteriskWildcard(int $index, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->select(AsteriskWildcard())->from(Alias(Table("test"), "c"));
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }
    
    /**
     * @covers ::select
     * 
     * @dataProvider provideQueryBuilder
     */
    public function testJoinClause(int $index, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->select()->from(Alias(Table("customer"), "c"))
                ->join(Alias(Table("order"), "o"))
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
        $queryBuilder->select()->from(Alias(Table("customer"), "c"))
                ->join(Alias(Table("order"), "o"))->on()
                    ->criteria(Equals(Field("id", "c"), Field("customer", "o")))
                    ->and()
                    ->criteria(Equals(Parameter("name"), Literal(10)))
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
        $queryBuilder->select()->from(Alias(Table("customer"), "c"))
                ->join(Alias(Table("order"), "o"))->on()
                    ->criteria(Equals(Field("id", "c"), Field("customer", "o")))
                    ->and()
                    ->group()
                        ->criteria(Greater(Field("id", "o"), Literal(100)))
                        ->and()
                        ->criteria(Lower(Field("id", "o"), Literal(1000)))
                    ->closeGroup()
                    ->or()
                    ->group()
                        ->criteria(Between(Field("id", "c"), Literal(1), Literal(10)))
                        ->and()
                        ->criteria(Between(Field("id", "o"), Literal(0), Literal(100)))
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
        $queryBuilder->select()->from(Table("customer"))
                ->where()
                    ->criteria(Equals(Field("id"), Field("old_identifier")))
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
        $queryBuilder->select()->from(Alias(Table("customer"), "c"))
                ->where()
                    ->criteria(Equals(Field("target_group", "c"), Literal(10)))
                    ->and()
                    ->group()
                        ->criteria(Equals(Field("name"), Literal("jochen")))
                    ->closeGroup()
                    ->or()
                    ->group()
                        ->criteria(Between(Parameter(), Literal(1), Literal(2)))
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
        $subQuery = $subQB->selectDistinct(Field("age"));
        $subQuery->from(Table("order"));
        
        $queryBuilder->select()->from(Alias(Table("customer"), "c"))
                ->where()
                    ->criteria(InValues(Field("id", "c"), [10,20,30]))
                    ->and()
                    ->criteria(InSubquery(Field("age"), $subQuery))
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
        $queryBuilder->select(Sum(Field("age")), Field("type"))->from(Table("customer"))
                ->join(Table("order"))->on()->criteria(Equals(Field("id", "customer"), Field("customer_id", "order")))
                ->where()
                    ->criteria(InValues(Field("id"), [10,20,30]))
                    ->and()
                    ->criteria(InValues(Field("id2"), [30,20,10]))
                ->groupBy(Field("type"))
                ->having()
                    ->criteria(GreaterEquals(Field("test"), Literal(10)))
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
        $queryBuilder->update()->table("customer")
                ->set("name", Parameter())
                ->set("zip", Parameter())
                ->where()
                    ->criteria(Equals(Field("id"), Literal(1)))
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
        $queryBuilder->delete()->fromTable("customer")
                ->where()
                    ->criteria(Equals(Field("id"), Literal(1)))
                ;
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }
    
    /**
     * @covers ::insert
     * 
     * @dataProvider provideQueryBuilder
     */
    public function testInsertWithSetClause(int $index, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->insert()->intoTable("customer")
                ->set("name", Literal("Simon Weiguny"))
                ->set("dateOfRegistry", Literal("2018-10-10"))
                ;
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }
    
    /**
     * @covers ::insert
     * 
     * @dataProvider provideQueryBuilder
     */
    public function testInsertWithValues(int $index, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->insert()->intoTable("customer")
                ->values(NullValue(), Literal("Simon Weiguny"), Literal("2018-10-10"))
                ;
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }
    
    /**
     * @covers ::insert
     * 
     * @dataProvider provideQueryBuilder
     */
    public function testInsertWithQuery(int $index, QueryBuilder $queryBuilder): void
    {
        $subQB = clone $queryBuilder;
        $subQuery = $subQB->select();
        $subQuery->from(Table("customer"))->where()->criteria(Equals(Field("id"), Literal(1)));
        
        $queryBuilder->insert()->intoTable("customer")
                ->query($subQuery)
                ;
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }

}

?>
