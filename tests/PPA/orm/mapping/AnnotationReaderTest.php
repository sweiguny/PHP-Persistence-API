<?php

namespace PPA\tests\orm\mapping;

use PHPUnit\Framework\TestCase;
use PPA\orm\mapping\AnnotationReader;
use PPA\tests\bootstrap\entity\DocCommentProvider;
use ReflectionClass;

/**
 * @coversDefaultClass AnnotationReader
 */
class AnnotationReaderTest extends TestCase
{
    /**
     *
     * @var AnnotationReader
     */
    private $annotationReader;
    
    /**
     *
     * @var ReflectionClass
     */
    private $reflectionClass;

    protected function setUp()
    {
        $this->annotationReader = new AnnotationReader();
        $this->reflectionClass  = new ReflectionClass($this->annotationReader);
    }
    
    /**
     * @covers ::filterAnnotations
     * @covers ::extractAnnotations
     * @covers ::fetchAnnotations
     * 
     * @dataProvider getDocComments
     */
    public function testFetchAnnotations(string $testname, string $docComment, array $expected)
    {
        $reflectionMethod = $this->reflectionClass->getMethod("fetchAnnotations");
        $reflectionMethod->setAccessible(true);
        
        $annotations = $reflectionMethod->invoke($this->annotationReader, $docComment);
        
        $this->assertEquals($expected, $annotations, $testname);
    }
    
    /**
     * 
     * @return array
     */
    public function getDocComments(): array
    {
        $docComments        = [];
        $docCommentProvider = new DocCommentProvider();
        $reflectionClass    = new ReflectionClass($docCommentProvider);
        
        foreach ($reflectionClass->getProperties() as $property)
        {
            $propName   = $property->getName();
            $docComment = $property->getDocComment();
            $expected   = $property->getValue($docCommentProvider);
            
            $docComments[] = [$propName, $docComment, $expected];
        }
        
        return $docComments;
    }
    
}

?>
