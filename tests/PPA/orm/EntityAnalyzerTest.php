<?php

namespace PPA\tests\orm;

use PHPUnit\Framework\TestCase;
use PPA\orm\entity\Serializable;
use PPA\orm\EntityAnalyzer;
use PPA\tests\bootstrap\entity\Customer;

class EntityAnalyzerTest extends TestCase
{
    /**
     *
     * @var EntityAnalyzer
     */
    private $entityAnalyzer;
    

    protected function setUp()
    {
        $this->entityAnalyzer       = new EntityAnalyzer();
    }
    
    /**
     * 
     * @dataProvider provideEntities
     */
    public function testAnalyze(Serializable $entity)
    {
        $this->assertTrue(true);
//        $result = $this->entityAnalyzer->analyze($entity);
        
    }
    
    public function provideEntities()
    {
        return [
            [new Customer(1, "John", "Doe", "at home")]
        ];
    }
    
}

?>
