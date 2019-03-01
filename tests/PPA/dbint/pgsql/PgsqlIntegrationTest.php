<?php

namespace PPA\tests\dbint\pgsql;

use PPA\dbal\DriverManager;
use PPA\dbal\query\builder\QueryBuilder;
use PPA\tests\bootstrap\DatabaseTestCase;
use function PPA\dbal\query\builder\AST\catalogObjects\Field;
use function PPA\dbal\query\builder\AST\expressions\Literal;
use function PPA\dbal\query\builder\AST\operators\Equals;

class PgsqlIntegrationTest extends DatabaseTestCase
{
    /**
     *
     * @var QueryBuilder
     */
    private $queryBuilder;
    
    public static function setUpBeforeClass(): void
    {
        self::$drivername = DriverManager::PGSQL;
        
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

//    public function provideQueryBuilder(): array
//    {
//        echo __METHOD__."\n";
//        return [new QueryBuilder(self::$connection->getDriver())];
//    }
    
//    public function testSimon(/*QueryBuilder $queryBuilder*/)
//    {
//        $this->queryBuilder->delete()->fromTable("addr_country")->where()->criteria(Equals(Field("id"), Literal(1)));
//        
//        $sql = $this->queryBuilder->sql();
//        
//        $affectedRows = self::$connection->getPdo()->exec($sql);
//        
//        $this->assertTrue($affectedRows === 1, "Expected that 1 row is deleted, but instead was '{$affectedRows}'.");
//    }
    
}

?>
