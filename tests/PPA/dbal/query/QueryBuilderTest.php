<?php

namespace PPA\tests\dbal;

use PHPUnit\Framework\TestCase;
use PPA\dbal\DriverManager;
use PPA\dbal\drivers\concrete\MySQLDriver;
use PPA\dbal\drivers\concrete\PgSQLDriver;
use PPA\dbal\query\builder\QueryBuilder;
use PPA\tests\bootstrap\ExpectedSQLResultsProvider;
use function PPA\dbal\query\builder\AST\catalogObjects\Field;
use function PPA\dbal\query\builder\AST\catalogObjects\Table;
use function PPA\dbal\query\builder\AST\expressions\functions\aggregate\Count;
use function PPA\dbal\query\builder\AST\expressions\functions\aggregate\Max;
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
use function PPA\dbal\query\builder\AST\operators\Like;
use function PPA\dbal\query\builder\AST\operators\Lower;
use function PPA\dbal\query\builder\AST\operators\NullValue;

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
    protected static $expectedResults = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        
        self::$expectedResults = ExpectedSQLResultsProvider::provideExpectedSQLResults();
    }
    
    public function provideQueryBuilder(): array
    {
        // TODO: When PHP provides covariance, refactor QueryBuilder :)
        return [
            DriverManager::MYSQL => [1, new QueryBuilder(new MySQLDriver())],
            DriverManager::PGSQL => [2, new QueryBuilder(new PgSQLDriver())]
        ];
    }
    
    private function checkResult(string $testCase, int $index, string $sql): void
    {
        if (!isset(self::$expectedResults[$testCase]))
        {
            throw new \Exception("Test case '{$testCase}' not defined in expected.csv. If you are sure, the test case is defined, please check the delimiter of the test file. It should be ';'.");
        }
        
        $expected = self::$expectedResults[$testCase][$index + 1];
        
        $this->assertEquals($expected, $sql);
    }
    
    /**
     * @covers ::select
     * @group singleton
     * @dataProvider provideQueryBuilder
     */
    public function testSelectAll(int $index, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->select()->from(Alias(Table("addr_country"), "c"));
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }
    
    /**
     * @covers ::select
     * @group singleton
     * @dataProvider provideQueryBuilder
     */
    public function testSelectAllWithAsteriskWildcard(int $index, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->select(AsteriskWildcard())->from(Alias(Table("addr_country"), "c"));
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }
    
    /**
     * @covers ::insert
     * 
     * @dataProvider provideQueryBuilder
     */
    public function testSelectWithAggregateFunctions(int $index, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->select(Count(AsteriskWildcard()), Max(Field("id")))->from(Alias(Table("addr_country"), "c"))
                ;
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }
    
    /**
     * @covers ::select
     * 
     * @dataProvider provideQueryBuilder
     */
    public function testJoinClause(int $index, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->select()->from(Alias(Table("addr_country"), "c"))
                ->join(Alias(Table("addr_state"), "s"))
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
        $queryBuilder->select()->from(Alias(Table("addr_country"), "c"))
                ->join(Alias(Table("addr_state"), "s"))->on()
                    ->criteria(Equals(Field("id", "c"), Field("country", "s")))
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
        $queryBuilder->select()->from(Alias(Table("addr_country"), "c"))
                ->join(Alias(Table("addr_state"), "s"))->on()
                    ->criteria(Equals(Field("id", "c"), Field("country", "s")))
                    ->and()
                    ->group()
                        ->criteria(Greater(Field("id", "s"), Literal(100)))
                        ->and()
                        ->criteria(Lower(Field("id", "s"), Literal(1000)))
                    ->closeGroup()
                    ->or()
                    ->group()
                        ->criteria(Between(Field("id", "s"), Literal(1), Literal(10)))
                        ->and()
                        ->criteria(Between(Field("id", "s"), Literal(0), Literal(100)))
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
        $queryBuilder->select()->from(Table("addr_country"))
                ->where()
                    ->criteria(Equals(Field("id"), Field("name")))
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
        $queryBuilder->select()->from(Alias(Table("addr_country"), "c"))
                ->where()
                    ->criteria(Equals(Field("id", "c"), Literal(10)))
                    ->and()
                    ->group()
                        ->criteria(Equals(Field("name"), Literal("Österreich")))
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
        $subQuery = $subQB->selectDistinct(Field("state"));
        $subQuery->from(Table("addr_district"))
                ->where()->criteria(Like(Field("name"), Literal("%heim%")));
        
        $queryBuilder->select()->from(Alias(Table("addr_state"), "s"))
                ->where()
                    ->criteria(InValues(Field("name", "s"), ["Bayern", "Niedersachsen"]))
                    ->and()
                    ->criteria(InSubquery(Field("id"), $subQuery))
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
        $queryBuilder->select(Sum(Field("zip_code")), Field("country"))->from(Table("address"))
                ->join(Table("addr_city"))->on()->criteria(Equals(Field("id", "addr_city"), Field("city", "address")))
                ->where()
                    ->criteria(InValues(Field("country"), [2, 5, 6]))
//                    ->and()
//                    ->criteria(InValues(Field("id2"), [30,20,10]))
                ->groupBy(Field("country"))
                ->having()
                    ->criteria(GreaterEquals(Count(Field("city")), Literal(3)))
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
        $queryBuilder->update()->table("addr_city")
                ->set("name", Parameter())
                ->set("zip_code", Parameter())
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
        $queryBuilder->delete()->fromTable("addr_city")
                ->where()
                    ->criteria(Equals(Field("id"), Literal(1)))
                    // TODO: If feature covariance exists, please refactor the way, so that here having and groupBy and so on can't be called.
                    // ->criteria(...)->having(...) is not allowwed...
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
        $queryBuilder->insert()->intoTable("addr_city")
                ->set("id", Literal(1))
                ->set("name", Literal("Simon Weiguny"))
                ->set("zip_code", Literal(1337))
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
        $queryBuilder->insert()->intoTable("address")
                ->values(NullValue(), Literal(1), Literal(18358), Literal(50), Literal("66a, Salmesmühle"))
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
        $subQuery->from(Table("address"))->where()->criteria(Equals(Field("id"), Literal(80)));
        
        $queryBuilder->insert()->intoTable("address")
                ->query($subQuery)
                ;
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }
    
}

?>
