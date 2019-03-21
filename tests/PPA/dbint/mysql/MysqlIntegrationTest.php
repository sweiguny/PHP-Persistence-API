<?php

namespace PPA\tests\dbint\mysql;

use PPA\dbal\DriverManager;
use PPA\tests\bootstrap\DatabaseIntegrationTestCase;

/**
 * @runTestsInSeparateProcesses
 */
class MysqlIntegrationTest extends DatabaseIntegrationTestCase
{
    
    protected static function getDriver(): string
    {
        return DriverManager::MYSQL;
    }

//    public function testSimon1()
//    {
//        $this->assertTrue(true);
//    }
//
//    public function testSimon2()
//    {
//        $this->assertTrue(true);
//    }
//
//    public function testSimon3()
//    {
//        $this->assertTrue(true);
//    }
    
}

?>
