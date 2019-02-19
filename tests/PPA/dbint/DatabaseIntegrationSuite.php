<?php

namespace PPA\tests\dbint;

use PHPUnit\Framework\TestSuite;
use PPA\tests\dbint\mysql\MysqlIntegrationSuite;
use PPA\tests\dbint\postgre\PostgreIntegrationSuite;

class DatabaseIntegrationSuite extends TestSuite
//class DatabaseIntegrationSuite extends \PHPUnit\Framework\TestCase
{
//    public static function suite()
//    {
//        $suite = new self();
//        $suite->addTestSuite(new MysqlIntegrationSuite());
//        $suite->addTestSuite(new PostgreIntegrationSuite());
//        return $suite;
//    }
    
    public function setUp()
    {
        echo "setUp DatabaseIntegrationSuite\n";
    }
    
    public function tearDown()
    {
        echo "setUp DatabaseIntegrationSuite\n";
    }

    public function testAnything()
    {
        echo "test Anything\n";
    }
    
}

?>
