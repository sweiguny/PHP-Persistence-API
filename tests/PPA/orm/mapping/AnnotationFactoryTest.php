<?php

namespace PPA\tests\orm\mapping;

use PHPUnit\Framework\TestCase;
use PPA\core\exceptions\logic\ParameterRequiredException;
use PPA\core\exceptions\logic\UnknownParametersException;
use PPA\core\exceptions\logic\WrongTargetClassException;
use PPA\core\exceptions\logic\WrongTargetPropertyException;
use PPA\orm\entity\Serializable;
use PPA\orm\mapping\AnnotationFactory;
use PPA\orm\mapping\AnnotationLoader;
use PPA\orm\mapping\AnnotationReader;
use PPA\orm\mapping\annotations\Column;
use PPA\orm\mapping\annotations\Table;
use PPA\tests\bootstrap\annotations\TestAnnotation;
use PPA\tests\bootstrap\annotations\UnknownParametersAnnotation;
use PPA\tests\bootstrap\entity\TargetClassWrong;
use PPA\tests\bootstrap\entity\TargetPropertyWrong;
use PPA\tests\bootstrap\entity\TestDefaultsEntity;

class AnnotationFactoryTest extends TestCase
{
    /**
     *
     * @var Serializable
     */
    private $dummyEntity;
    
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

    protected function setUp()
    {
        $this->dummyEntity       = new class() implements Serializable {};
        $this->annotationFactory = new AnnotationFactory();
        $this->annotationReader  = new AnnotationReader();
        $this->annotationLoader  = new AnnotationLoader();
    }
    
    public function testInstantiateNormal()
    {
        $parameters = [
            "value1" => 1, // shall be set by constructor
            "value2" => 2, // shall be set by constructor
            "value3" => 3  // shall be set by setter (and hence multiplied by 2)
                           // value4 shall not be set
        ];
        
        /* @var $testAnnotation TestAnnotation */
        $testAnnotation = $this->annotationFactory->instantiate($this->dummyEntity, TestAnnotation::class, $parameters);
        
        $this->assertEquals($testAnnotation->getValue1(), 1);
        $this->assertEquals($testAnnotation->getValue2(), 2);
        $this->assertEquals($testAnnotation->getValue3(), 6);
        $this->assertEquals($testAnnotation->getValue4(), 0);
    }
    
    public function testInstatiateUnknownParameters()
    {
        $parameters = [
            "value1" => 1,
            "value2" => 2
        ];
        
        $this->expectException(UnknownParametersException::class);
        
        $this->annotationFactory->instantiate($this->dummyEntity, UnknownParametersAnnotation::class, $parameters);
    }
    
    public function testRequiredParameter()
    {
        $parameters = [
            "value1" => 1,
            "value2" => 2
        ];
        
        $this->expectException(ParameterRequiredException::class);
        
        $this->annotationFactory->instantiate($this->dummyEntity, TestAnnotation::class, $parameters);
    }
    
    public function testDefaults()
    {
        $entity = new TestDefaultsEntity();
        $fqcn   = explode("\\", get_class($entity));
        $result = $this->annotationLoader->load($this->annotationReader->read($entity));
        
        $classAnnotations    = $result->getClassAnnotations();
        $propertyAnnotations = $result->getPropertyAnnotations();
        
        $this->assertEquals(strtolower(array_pop($fqcn)), $classAnnotations[Table::class]->getName());
        $this->assertEquals($entity->getColumn(), $propertyAnnotations["column"][Column::class]->getName());
    }
    
    public function testWrongTargetProperty()
    {
        $this->expectException(WrongTargetPropertyException::class);
        
        $this->annotationLoader->load($this->annotationReader->read(new TargetClassWrong()));
    }
    
    public function testWrongTargetClass()
    {
        $this->expectException(WrongTargetClassException::class);
        
        $this->annotationLoader->load($this->annotationReader->read(new TargetPropertyWrong()));
    }
    
}

?>
