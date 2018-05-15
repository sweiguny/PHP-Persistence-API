<?php

namespace PPA\tests\dbal;

use PHPUnit\Framework\TestCase;
use PPA\core\exceptions\io\IOException;
use PPA\core\exceptions\runtime\InvalidQueryBuilderStateException;
use PPA\dbal\drivers\concrete\MySQLDriver;
use PPA\dbal\query\builder\QueryBuilder;
use PPA\tests\bootstrap\DummyDriver;

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
    
    private static function readExpectedCSv(string $filepath): \Generator
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
        $offset = 1;
        
        $this->assertEquals(self::$expectedCsvData[$testCase][$offset + $index], $sql);
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
    
    /**
     * @covers ::select
     * 
     * @dataProvider provideQueryBuilder
     */
    public function testSelect(int $index, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->select()->fromTable("test", "c");
        
        $this->checkResult(__FUNCTION__, $index, $queryBuilder->sql());
    }
    
    public function testJoin(): void
    {
        $qb = new QueryBuilder(new DummyDriver());
        $qb->select()->fromTable("customer", "c");
        
        $this->markTestIncomplete("not yet implemented");
    }
    
    public function testOn(): void
    {
        $this->markTestIncomplete("not yet implemented");
    }
    
    public function testSQL(): void
    {
//        $qb = new QueryBuilder(new DummyDriver());
//        $qb->select()->fromTable("customer", "c")
//                ->join("order", "o")->on()
//                    ->withField("age", "c")->betweenLiteral(10)->andParameter()
//                    ->andWithField("order", "x")->inLiterals([1,2,3])
//                    ->andWithField("id", "c")->equals("cid", "o")
//                    ->andGroup()
//                        ->withField("test")->equals(10)
//                        ->orWithLiteral(100)->betweenParameter()->andParameter()
//                        ->endGroup()
//                    ->end()
//                ->where()
//                    ->withLiteral("hudriwudri")->equals("hudriwudri")
//                    ->andWithParameter()->equals("test")
//                    ->andWithParameter("nameIT")->equals("test2")
//                    ->andGroup()
//                        ->withField("test")->equals(10)
//                        ->orWithLiteral(100)->betweenParameter()->andParameter()
//                        ->orGroup()
//                            ->withField("test1")->equals(10)
//                            ->andWithField("test2")->equals(20)
//                            ->andWithField("test3")->equals(30)
//                            ->endGroup()
//                        ->endGroup()
//                    ->end()
////                ->orderBy()
//                ;
//        
////        $qb->select()->fromTable($tableName)->where()->end();
//        
//        $sql = $qb->sql();
//        
//        $this->markTestIncomplete("not yet implemented");
    }

}

?>
