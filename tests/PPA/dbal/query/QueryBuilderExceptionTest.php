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
            "mysql" => [1, new QueryBuilder(new MySQLDriver())]
        ];
    }
    
    
    /**
     * @covers ::select
     */
    public function testDoubleSelect(): void
    {
        $this->expectException(InvalidQueryBuilderStateException::class);
        
        $queryBuilder = new QueryBuilder(new DummyDriver());
        $queryBuilder->select()->fromTable("table", "c");
        
        $queryBuilder->select();
    }
    
    public function testOnGroupFailure(): void
    {
        $this->expectException(CollectionStateException::class);
        
        $queryBuilder = new QueryBuilder(new DummyDriver());
        $queryBuilder->select()->fromTable("customer", "c")
                ->join("order", "o")->on()
                    ->group()
                ;
        
        $queryBuilder->sql();
    }
    
    public function testCriteriaFailure(): void
    {
        $this->expectException(CollectionStateException::class);
        
        $queryBuilder = new QueryBuilder(new DummyDriver());
        $queryBuilder->select()->fromTable("customer", "c")
                ->where()->withField("id")
                ;
        
        $queryBuilder->sql();
    }

}

?>
