<?php

namespace PPA\tests\bootstrap;

use PPA\core\exceptions\io\IOException;
use PPA\core\util\FileReader;
use const PPA_TEST_BOOTSTRAP_PATH;

class ExpectedSQLResultsProvider
{
    /**
     *
     * @var string
     */
    private static $filename = "expected.csv";

    /**
     *
     * @var array
     */
    private static $expectedResults = [];

    private static function createFilePathToExpectedResultsFile(): string
    {
        if (!file_exists($filepath = PPA_TEST_BOOTSTRAP_PATH . DIRECTORY_SEPARATOR . self::$filename))
        {
            throw new IOException("File '{$filepath}' does not exist.");
        }
        
        return $filepath;
    }
    
    private static function gatherExcpectedSQLResults()
    {
        $filepath = self::createFilePathToExpectedResultsFile();
        $iterator = (new FileReader())->getLineIterator($filepath);
        $index    = 0;
        
        foreach ($iterator as $iteration)
        {
            if ($index++ > 0) // skip header
            {
                $temp = explode(";", $iteration);
                self::$expectedResults[array_shift($temp)] = $temp;
            }
        }
    }
    
    public static function provideExpectedSQLResults(): array
    {
        if (empty(self::$expectedResults))
        {
            self::gatherExcpectedSQLResults();
        }
        
        return self::$expectedResults;
    }
    
}

?>
