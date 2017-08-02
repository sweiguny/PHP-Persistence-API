<?php

namespace PPA\tests\orm\mapping;

use PHPUnit\Framework\TestCase;
use PPA\orm\mapping\AnnotationLoader;
use PPA\orm\mapping\AnnotationReader;
use PPA\tests\bootstrap\entity\Order;

class AnnotationLoaderTest extends TestCase
{
    
    public function testLoad()
    {
        $entity = new Order();
        
        $annotationReader = new AnnotationReader();
        $annotationLoader = new AnnotationLoader();
        
        $annotationLoader->load($annotationReader->read($entity));
        
    }
    
}

?>
