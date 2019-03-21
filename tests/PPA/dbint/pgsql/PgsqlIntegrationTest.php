<?php

namespace PPA\tests\dbint\pgsql;

use PPA\dbal\DriverManager;
use PPA\tests\bootstrap\DatabaseIntegrationTestCase;
use function PPA\dbal\query\builder\AST\catalogObjects\Field;
use function PPA\dbal\query\builder\AST\expressions\Literal;
use function PPA\dbal\query\builder\AST\operators\Equals;

/**
 * @runTestsInSeparateProcesses
 */
class PgsqlIntegrationTest extends DatabaseIntegrationTestCase
{

    protected static function getDriver(): string
    {
        return DriverManager::PGSQL;
    }
    
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
