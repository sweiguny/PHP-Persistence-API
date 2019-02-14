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

/**
 * @coversDefaultClass PPA\orm\mapping\AnnotationFactory
 */
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

    protected function setUp(): void
    {
        $this->dummyEntity       = get_class(new class() implements Serializable {});
        $this->annotationFactory = new AnnotationFactory();
        $this->annotationReader  = new AnnotationReader();
        $this->annotationLoader  = new AnnotationLoader();
    }
    
    public function testInstantiateNormal(): void
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
    
    public function testInstatiateUnknownParameters(): void
    {
        $parameters = [
            "value1" => 1,
            "value2" => 2
        ];
        
        $this->expectException(UnknownParametersException::class);
        
        $this->annotationFactory->instantiate($this->dummyEntity, UnknownParametersAnnotation::class, $parameters);
    }
    
    public function testRequiredParameter(): void
    {
        $parameters = [
            "value1" => 1,
            "value2" => 2
        ];
        
        $this->expectException(ParameterRequiredException::class);
        
        $this->annotationFactory->instantiate($this->dummyEntity, TestAnnotation::class, $parameters);
    }
    
    public function testDefaults(): void
    {
        $entity    = new TestDefaultsEntity();
        $className = get_class($entity);
        $fqcn      = explode("\\", $className);
        
        $annotationBag       = $this->annotationLoader->load($className, $this->annotationReader->readFromObject($entity));
        $classAnnotations    = $annotationBag->getClassAnnotations();
        $propertyAnnotations = $annotationBag->getPropertyAnnotations();
        
        $this->assertEquals(strtolower(array_pop($fqcn)), $classAnnotations[Table::class]->getName());
        $this->assertEquals($entity->getColumn(), $propertyAnnotations["column"][Column::class]->getName());
    }
    
    public function testWrongTargetProperty(): void
    {
        $this->expectException(WrongTargetPropertyException::class);
        
        $classname = TargetClassWrong::class;
        
        $this->annotationLoader->load($classname, $this->annotationReader->readFromAnnotatableClass($classname));
    }
    
    public function testWrongTargetClass(): void
    {
        $this->expectException(WrongTargetClassException::class);
        
        $classname = TargetPropertyWrong::class;
        
        $this->annotationLoader->load($classname, $this->annotationReader->readFromAnnotatableClass($classname));
    }
    
}

?>
