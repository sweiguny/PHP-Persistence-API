<?php

namespace PPA\tests\dbal;

use Generator;
use PHPUnit\Framework\TestCase;
use PPA\core\exceptions\io\IOException;
use PPA\core\exceptions\runtime\CollectionStateException;
use PPA\core\exceptions\runtime\InvalidQueryBuilderStateException;
use PPA\dbal\drivers\concrete\MySQLDriver;
use PPA\dbal\query\builder\QueryBuilder;
use PPA\tests\bootstrap\DummyDriver;

/**
 * @coversDefaultClass \PPA\dbal\query\builder\QueryBuilder
 */
class QueryBuilderExceptionTest extends TestCase
{
    
    
    public function provideQueryBuilder(): array
    {
        return [
            [new QueryBuilder(new DummyDriver())]
        ];
    }
    
    /**
     * @covers ::select
     * 
     * @dataProvider provideQueryBuilder
     */
    public function testDoubleSelect(QueryBuilder $queryBuilder): void
    {
        $this->expectException(InvalidQueryBuilderStateException::class);
        
        $queryBuilder->select()->fromTable("table", "c");
        
        $queryBuilder->select();
    }
    
    /**
     * @covers ::select
     * 
     * @dataProvider provideQueryBuilder
     */
    public function testOnGroupFailure(QueryBuilder $queryBuilder): void
    {
//        $this->expectException(CollectionStateException::class);
        
        $queryBuilder->select()->fromTable("customer", "c")
                ->join("order", "o")->on()
//                    ->group()
                ;
        
        echo $queryBuilder->sql();
    }
    
    /**
     * @covers ::select
     * 
     * @dataProvider provideQueryBuilder
     */
    public function testCriteriaFailure(QueryBuilder $queryBuilder): void
    {
        $this->expectException(CollectionStateException::class);
        $this->expectExceptionCode(CollectionStateException::CODE_CRITERIA_DIRTY);
        
        $queryBuilder->select()->fromTable("customer", "c")
                ->where()->withField("id")
                ;
        
        $queryBuilder->sql();
    }

}

?>
