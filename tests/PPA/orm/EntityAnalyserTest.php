<?php

namespace PPA\tests\orm;

use PHPUnit\Framework\TestCase;
use PPA\core\exceptions\logic\TableAnnotationMissingException;
use PPA\orm\entity\Serializable;
use PPA\orm\EntityAnalyser;
use PPA\tests\bootstrap\entity\Customer;
use PPA\tests\bootstrap\entity\NoTableAnnotation;

/**
 * @coversDefaultClass PPA\orm\EntityAnalyser
 */
class EntityAnalyserTest extends TestCase
{
    /**
     *
     * @var EntityAnalyser
     */
    private $entityAnalyser;
    
    protected function setUp(): void
    {
        $this->entityAnalyser = new EntityAnalyser();
    }
    
    /**
     * @covers ::analyse
     * 
     * @dataProvider provideEntities
     */
    public function testAnalyse(Serializable $entity, ?string $expectedException, string $primaryPropertyName): void
    {
        if ($expectedException != null)
        {
            $this->expectException($expectedException);
        }
        
        $analysis = $this->entityAnalyser->analyse($entity, get_class($entity));
        
        $this->assertEquals($primaryPropertyName, $analysis->getPrimaryProperty()->getName());
    }
    
    public function provideEntities(): array
    {
        return [
            [new Customer(1, "John", "Doe", "at home"), null, "customerNo"],
            [new NoTableAnnotation(1, "John", "Doe", "at home"), TableAnnotationMissingException::class, "customerNo"]
        ];
    }
    
}

?>
