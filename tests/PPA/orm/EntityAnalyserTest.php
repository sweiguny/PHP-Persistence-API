<?php

namespace PPA\tests\orm;

use PHPUnit\Framework\TestCase;
use PPA\core\exceptions\logic\TableAnnotationMissingException;
use PPA\orm\entity\Serializable;
use PPA\orm\EntityAnalyser;
use PPA\tests\bootstrap\entity\Customer;
use PPA\tests\bootstrap\entity\NoTableAnnotation;

class EntityAnalyserTest extends TestCase
{
    /**
     *
     * @var EntityAnalyser
     */
    private $entityAnalyser;
    

    protected function setUp()
    {
        $this->entityAnalyser       = new EntityAnalyser();
    }
    
    /**
     * 
     * @dataProvider provideEntities
     */
    public function testAnalyse(Serializable $entity, ?string $expectedException)
    {
        if ($expectedException != null)
        {
            $this->expectException($expectedException);
        }
        
        $analysis = $this->entityAnalyser->analyse($entity, get_class($entity));
        
        print_r($analysis);
        
        $this->assertTrue(true);
    }
    
    public function provideEntities()
    {
        return [
            [new Customer(1, "John", "Doe", "at home"), null],
            [new NoTableAnnotation(1, "John", "Doe", "at home"), TableAnnotationMissingException::class]
        ];
    }
    
}

?>
