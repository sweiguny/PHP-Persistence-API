<?php

namespace PPA\tests\orm\mapping;

use PHPUnit\Framework\TestCase;
use PPA\orm\entity\Serializable;
use PPA\orm\mapping\AnnotationBag;
use PPA\orm\mapping\AnnotationLoader;
use PPA\orm\mapping\AnnotationReader;
use PPA\tests\bootstrap\entity\Customer;
use ReflectionClass;
use ReflectionMethod;

/**
 * @coversDefaultClass PPA\orm\mapping\AnnotationLoader
 */
class AnnotationLoaderTest extends TestCase
{
    /**
     *
     * @var ReflectionMethod
     */
    private $methodHasNamespace;

    /**
     *
     * @var AnnotationLoader
     */
    private $annotationLoader;
    
    /**
     * Set up one instance of AnnotationLoader to avoid countless intatiations in one test.
     * Make private methods accessible.
     */
    protected function setUp(): void
    {
        $this->annotationLoader = new AnnotationLoader();
        
        $reflectionClass = new ReflectionClass($this->annotationLoader);
        
        $this->methodHasNamespace = $reflectionClass->getMethod("hasNamespace");
        $this->methodHasNamespace->setAccessible(true);
    }
    
    /**
     * @covers ::load
     * 
     * @dataProvider provideEntities
     */
    public function testLoad(Serializable $entity): void
    {
        $annotationReader = new AnnotationReader();
        $annotationLoader = new AnnotationLoader();
        
        $declaredClasses   = get_declared_classes();
        $annotationBag     = $annotationLoader->load($annotationReader->read($entity));
        $loadedAnnotations = $this->getClassList($annotationBag);
        
        $differenceCheck = array_diff($declaredClasses, $loadedAnnotations);
        $counterCheck    = array_diff($loadedAnnotations, $declaredClasses);
        
        $this->assertEmpty($counterCheck);
        $this->assertEquals(count($differenceCheck), count($declaredClasses) - count($loadedAnnotations));
    }
    
    private function getClassList(AnnotationBag $annotationBag): array
    {
        $classAnnotations    = $annotationBag->getClassAnnotations();
        $propertyAnnotations = $annotationBag->getPropertyAnnotations();
        $result              = $classAnnotations;
        
        foreach ($propertyAnnotations as $annotations)
        {
            array_merge($result, $annotations);
        }
        
        return array_unique(array_map("get_class", $result));
    }
    
    /**
     * @dataProvider provideClassnames
     */
    public function testHasNamespace(string $classname, bool $expected): void
    {
        $result = $this->methodHasNamespace->invoke($this->annotationLoader, $classname);
        
        $this->assertEquals($result, $expected);
    }
    
    public function provideClassnames(): array
    {
        return [
            ['PPA\orm\mapping\annotations\Table', true],
            ['\PPA\orm\mapping\annotations\Table', true],
            ['Column', false],
            ['\Column', true]
        ];
    }
    
    public function provideEntities(): array
    {
        return [
            [new Customer(1, "John", "Doe", "at home")]
//            new BadlyAnnotated()
        ];
    }
    
}

?>
