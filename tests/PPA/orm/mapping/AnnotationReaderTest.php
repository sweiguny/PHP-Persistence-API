<?php
declare(strict_types=1);

namespace PPA\tests\orm\mapping;

use PHPUnit\Framework\TestCase;
use PPA\orm\mapping\AnnotationReader;
use PPA\tests\bootstrap\entity\DocCommentProvider;
use ReflectionClass;
use ReflectionProperty;

/**
 * @coversDefaultClass PPA\orm\mapping\AnnotationReader
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

    protected function setUp(): void
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
    public function testFetchAnnotations(string $testname, string $docComment, array $expected): void
    {
//        echo "in ".__FUNCTION__."\n";
//        var_dump($docComment);echo "\n";
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
            /* @var $property ReflectionProperty */
            
            $propName   = $property->getName();
            $docComment = $property->getDocComment();
//            echo "in ".__FUNCTION__."\n";
//            var_dump($docComment);echo "\n";
            $expected   = $property->getValue($docCommentProvider);
            
            $docComments[] = [$propName, $docComment ?: "", $expected];
        }
        
        return $docComments;
    }
    
}

?>
