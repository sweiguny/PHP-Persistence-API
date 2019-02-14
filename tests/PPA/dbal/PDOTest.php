<?php

namespace PPA\tests\dbal;

use PHPUnit\DbUnit\DataSet\CsvDataSet;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PPA\core\exceptions\io\IOException;
use PPA\orm\mapping\AnnotationFactory;
use PPA\orm\mapping\AnnotationLoader;
use PPA\orm\mapping\AnnotationReader;
use PPA\tests\bootstrap\DatabaseTestCase;
use PPA\tests\bootstrap\entity\State;

class PDOTest extends DatabaseTestCase
{
    /**
     *
     * @var AnnotationFactory
     */
    private $annotationFactory;
    
    /**
     *
     * @var AnnotationReader
     */
    private $annotationReader;
    
    /**
     *
     * @var AnnotationLoader
     */
    private $annotationLoader;
    
    private static function createFilePathToFixtures(string $filename): string
    {
        if (!file_exists($filepath = PPA_BOOTSTRAP_PATH . DIRECTORY_SEPARATOR . "fixtures" . DIRECTORY_SEPARATOR . $filename))
        {
            throw new IOException("File '{$filepath}' does not exist.");
        }
        
        return $filepath;
    }
    
    public function setUp()
    {
        
        $this->annotationFactory = new AnnotationFactory();
        $this->annotationReader  = new AnnotationReader();
        $this->annotationLoader  = new AnnotationLoader();
    }
    
    protected function getDataSet(): IDataSet
    {
        
        
        $this->annotationLoader->load($this->annotationReader->read(new State()));
        
        $dataSet = new CsvDataSet(";");
        $dataSet->addTable("addr_state", dirname(__FILE__)."/_files/guestbook.csv");
        
        return $dataSet;
    }

}

?>
