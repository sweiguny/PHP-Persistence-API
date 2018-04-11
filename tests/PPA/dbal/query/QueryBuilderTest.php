<?php

namespace PPA\tests\dbal;

use PHPUnit\Framework\TestCase;
use PPA\dbal\Connection;
use PPA\dbal\drivers\concrete\MySQLDriver;
use PPA\dbal\query\builder\QueryBuilder;

/**
 * @coversDefaultClass \PPA\dbal\query\builder\QueryBuilder
 */
class QueryBuilderTest extends TestCase
{
    /**
     *
     * @var Connection
     */
//    private $connection;


//    protected function setUp(): void
//    {
//        if ($this->connection == null)
//        {
//            $driverName = $GLOBALS["driver"];
//            $username   = $GLOBALS["username"];
//            $password   = $GLOBALS["password"];
//            $database   = $GLOBALS["database"];
//            $hostname   = $GLOBALS["hostname"];
//            $port       = isset($GLOBALS["port"]) ?: null;
//
//            $this->connection = DriverManager::getConnection($driverName, [], $username, $password, $hostname, $database, $port);
//        }
//    }
    
    
    public function testSQL(): void
    {
        $qb = new QueryBuilder(new MySQLDriver());
        $qb->select()->fromTable("customer", "c")
                ->join("order", "o")->on()
                    ->with("age")->betweenLiteral(10)->andParameter()
                    ->andWith("x")->inLiterals([1,2,3])
                    ->andWith("c.id")->equals("o.cid")
                    ->end()
                ->where()
                    ->with("'hudriwudri'")->equals("'hudriwudri'")
                    ->end();
                ;
        
        $sql = $qb->sql();
        
        
        echo "******" . $sql;
    }

}

?>
