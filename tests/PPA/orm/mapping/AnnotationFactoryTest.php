<?php

namespace PPA\tests\orm\mapping;

use PHPUnit\Framework\TestCase;
use PPA\orm\mapping\AnnotationFactory;
use ReflectionClass;
use ReflectionMethod;

class AnnotationFactoryTest extends TestCase
{
    /**
     *
     * @var AnnotationFactory
     */
    private $annotationFactory;
    
    /**
     *
     * @var ReflectionMethod
     */
    private $methodGetConstructorParameters;

    /**
     *
     * @var ReflectionMethod
     */
    private $methodSetProperties;
    
    protected function setUp()
    {
        $this->annotationFactory = new AnnotationFactory();
        
        $reflectionClass = new ReflectionClass($this->annotationFactory);
        
        $this->methodGetConstructorParameters = $reflectionClass->getMethod("getConstructorParameters");
        $this->methodSetProperties            = $reflectionClass->getMethod("setProperties");
    }
    
    public function testInstantiateAnnotationWithConstructor()
    {
        
    }
    
    public function testInstantiateAnnotationWithEmptyConstructor()
    {
        
    }
    
    public function testInstantiateAnnotationWithoutConstructorBySetter()
    {
        
    }
    
    public function testInstantiateAnnotationWithoutConstructorByProperties()
    {
        
    }
    
    public function testInstantiateAll()
    {
        
    }
    
    
}

?>
