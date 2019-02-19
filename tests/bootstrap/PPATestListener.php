<?php

namespace PPA\tests\bootstrap;

use PHPUnit\Framework\BaseTestListener;
use PHPUnit\Framework\TestSuite;
use PPA\tests\dbint\DatabaseIntegrationSuite;
use PPA\tests\dbint\mysql\MysqlIntegrationSuite;
use PPA\tests\dbint\postgre\PostgreIntegrationSuite;

class PPATestListener extends BaseTestListener
{
    const SUITE_DEFAULT_ROOT   = "";
    const SUITE_DBAL           = "dbal";
    const SUITE_ORM            = "orm";
    const SUITE_DB_INTEGRATION = "dbint";
    
    /**
     * PHPUnit nests all test classes iteself into an own test suite. This test
     * suite gets the name of the test classes.
     * 
     * It adds them then to the defined test suites, which get the names of the config.
     * 
     * After that, the defined test suites are added to a root suite with an empty name.
     * Hence throwing an exception for unknown suites doesn't make much sense.
     * 
     * @param TestSuite $suite
     */
    public function startTestSuite(TestSuite $suite)
    {
//        print_r($suite); // No good idea, as output is MASSIVE.
//        echo print_r($suite->getName(), true)."\n";
        
        switch ($suite->getName())
        {
            case self::SUITE_DBAL:
//                echo "suite dbal";
                break;
            case self::SUITE_ORM:
//                echo "suite orm";
                break;
            case self::SUITE_DB_INTEGRATION:
                $this->prepareSuite($suite);
                $this->createFixturesForDBINT();
                break;
            case self::SUITE_DEFAULT_ROOT:
            default: break;
        }
    }
    
    public function endTestSuite(TestSuite $suite)
    {
        
    }

//    public function startTest(Test $test)
//    {
//    }
//
//    public function endTest(Test $test, $time)
//    {
//    }
    public function prepareSuite(TestSuite $suite)
    {
//        $dbSuite = new DatabaseIntegrationSuite();
//        $dbSuite->addTestSuite(new MysqlIntegrationSuite());
//        $dbSuite->addTestSuite(new PostgreIntegrationSuite());
//        $dbSuite->run();
        
//        $suite->addTestSuite($dbSuite);
//        $suite->addTestSuite(DatabaseIntegrationSuite::class);
//        $suite->addTestSuite(\PPA\tests\dbint\mysql\TestSomething2::class);
//        $suite->addTestSuite(\PPA\tests\dbint\postgre\TestSomething1::class);
//        $suite->
    }
    private function createFixturesForDBINT()
    {
        echo "create fixtures in listener\n";
    }
    
}

?>
