<?php

namespace PPA\tests\orm\mapping;

use PHPUnit\Framework\TestCase;
use PPA\orm\entity\Serializable;
use PPA\orm\mapping\AnnotationLoader;
use PPA\orm\mapping\AnnotationReader;
use PPA\tests\bootstrap\entity\BadlyAnnotated;
use PPA\tests\bootstrap\entity\WellAnnotated;
use ReflectionClass;
use ReflectionMethod;

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
    protected function setUp()
    {
        $this->annotationLoader = new AnnotationLoader();
        
        $reflectionClass = new ReflectionClass($this->annotationLoader);
        
        $this->methodHasNamespace = $reflectionClass->getMethod("hasNamespace");
        $this->methodHasNamespace->setAccessible(true);
    }
    
    /**
     * @dataProvider provideEntities
     */
    public function testLoad(Serializable $entity)
    {
//        AnnotationReader::addIgnore("xxx\Table");
        
        $annotationReader = new AnnotationReader();
        $annotationLoader = new AnnotationLoader();
        
        $loadedAnnotations = $annotationLoader->load($annotationReader->read($entity));
        print_r($loadedAnnotations);
        
//        $this->is
    }
    
    /**
     * @dataProvider provideClassnames
     */
    public function testHasNamespace(string $classname, bool $expected)
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
            [new WellAnnotated()]
//            new BadlyAnnotated()
        ];
    }
    
}

?>
