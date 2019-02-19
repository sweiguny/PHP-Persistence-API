<?php

namespace PPA\tests\dbint\postgre;

use PHPUnit\Framework\TestSuite;

class PostgreIntegrationSuite extends TestSuite
{
    public static function suite()
    {
        $suite = new self();
        $suite->addTestSuite('TestSomething1');
        $suite->addTestSuite('TestSomething2');
        return $suite;
    }
    
    public function setUp()
    {
        echo "setUp POSTGRE\n";
    }
    
    public function tearDown()
    {
        echo "tearDown POSTGRE\n";
    }

}

?>
