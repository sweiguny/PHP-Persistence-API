<?php

namespace PPA\tests\other;

use PHPUnit\Framework\TestCase;
use PPA\tests\bootstrap\entity\em\Entity2;
use PPA\tests\bootstrap\entity\shared\Catholic;
use stdClass;

class PHPInternalsTest extends TestCase
{
    
    public function testObjectHashes(): void
    {
        $object1 = new stdClass();
        $object2 = clone $object1;
        
        $this->assertFalse(spl_object_hash($object1) == spl_object_hash($object2));
    }
    
    public function testObjectReferences(): void
    {
        $object1 = new stdClass();
        $object2 = clone $object1;
        
        $this->assertTrue($object1 == $object2); // This is actually true...
    }
    
    public function testObjectValuesAfterClone(): void
    {
        $object1 = new stdClass();
        $object2 = clone $object1;
        
        // This is actually true, because we did not touch properties yet.
        $this->assertTrue($object1 == $object2);
        
        $object1->property1 = "prop1";
        $object2->property2 = "prop2";
        
//        var_dump($object1);
//        var_dump($object2);
        
        // This is actually false, because we did touch properties now.
        $this->assertFalse($object1 == $object2);
        
        $this->assertObjectNotHasAttribute("property2", $object1);
        $this->assertObjectNotHasAttribute("property1", $object2);
    }
    
    public function testObjectBeforeAfterClone(): void
    {
        $subObject1 = new stdClass();
        $subObject1->variable1 = "var1";
        
        $subObject2 = new stdClass();
        $subObject2->variable2 = "var2";
        
        $object1 = new stdClass();
        $object1->subObject1 = $subObject1;
        
        $object2 = clone $object1;
        $object2->subObject2 = $subObject2;
        
        // Shows that native cloning is shallow ...
        $this->assertEquals($object2->subObject1, $object1->subObject1);
        
        // ... but the clone's actually independent from the origin.
        $this->assertObjectNotHasAttribute("subObject2", $object1);
        
//        var_dump($object1);
//        var_dump($object2);
        
    }
    
    public function testTrackingOfChangesInObjectsUsingArray(): void
    {
        $entityCurrent = new Entity2("stringA", "stringB", 10, new stdClass());
        $entityOrigin  = clone $entityCurrent;
        
        $entityCurrent->setColumn3(11);
        $entityCurrent->setOneToOneRelation(new stdClass());
        
        $currentData  = (array)$entityCurrent;
        $originalData = (array)$entityOrigin;
        
//        print_r($currentData);
//        print_r($originalData);
//        var_dump($currentData, $originalData);
        
        $this->assertCount(6, $currentData);
        $this->assertCount(6, $originalData);
    }
    
    public function testTrackingOfChangesInObjectsUsingJson(): void
    {
        $entityCurrent = new Entity2("stringA", "stringB", 10, new stdClass());
        $entityOrigin  = clone $entityCurrent;
        
        $entityCurrent->setColumn3(11);
        $entityCurrent->setOneToOneRelation(new stdClass());
        
        $currentData  = json_encode($entityCurrent);
        $originalData = json_encode($entityOrigin);
        
        $this->assertCount(1, json_decode($currentData, true), "json_encode() should only encode public properties.");
        $this->assertCount(1, json_decode($originalData, true), "json_encode() should only encode public properties.");
//        print_r(json_decode($originalData, true));
    }
    
    public function testClassnames(): void
    {
        $this->assertEquals(get_class(new Catholic()), Catholic::class);
    }
    
}

?>
