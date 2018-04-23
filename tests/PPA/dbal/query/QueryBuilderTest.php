<?php

namespace PPA\tests\dbal;

use PHPUnit\Framework\TestCase;
use PPA\core\exceptions\runtime\InvalidQueryBuilderStateException;
use PPA\dbal\drivers\concrete\MySQLDriver;
use PPA\dbal\query\builder\QueryBuilder;

/**
 * @coversDefaultClass \PPA\dbal\query\builder\QueryBuilder
 */
class QueryBuilderTest extends TestCase
{
    
    public function testSelect(): void
    {
        $this->expectException(InvalidQueryBuilderStateException::class);
        
        $qb = new QueryBuilder(new MySQLDriver());
        $qb->select()->fromTable("customer", "c");
        
        $qb->select();
        
        
        
        $qb = new QueryBuilder(new MySQLDriver());
        $qb->select()->fromTable("customer", "c");
        echo "nochmal";
    }
    
    public function testJoin(): void
    {
        $qb = new QueryBuilder(new MySQLDriver());
        $qb->select()->fromTable("customer", "c");
        
        
    }
    
    public function testOn(): void
    {
        
    }
    
    public function testSQL(): void
    {
        $qb = new QueryBuilder(new MySQLDriver());
        $qb->select()->fromTable("customer", "c")
                ->join("order", "o")->on()
                    ->withField("age", "c")->betweenLiteral(10)->andParameter()
                    ->andWithField("order", "x")->inLiterals([1,2,3])
                    ->andWithField("id", "c")->equals("cid", "o")
                    ->andGroup()
                        ->withField("test")->equals(10)
                        ->orWithLiteral(100)->betweenParameter()->andParameter()
                        ->endGroup()
                    ->end()
                ->where()
                    ->withLiteral("hudriwudri")->equals("hudriwudri")
                    ->andWithParameter()->equals("test")
                    ->andWithParameter("nameIT")->equals("test2")
                    ->andGroup()
                        ->withField("test")->equals(10)
                        ->orWithLiteral(100)->betweenParameter()->andParameter()
                        ->orGroup()
                            ->withField("test1")->equals(10)
                            ->andWithField("test2")->equals(20)
                            ->andWithField("test3")->equals(30)
                            ->endGroup()
                        ->endGroup()
                    ->end()
//                ->orderBy()
                ;
        
//        $qb->select()->fromTable($tableName)->where()->end();
        
        $sql = $qb->sql();
        
        
//        echo "******\n\n\n" . $sql . "\n\n\n";
    }

}

?>
