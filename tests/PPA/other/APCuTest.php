<?php

namespace PPA\tests\other;

use PHPUnit\Framework\TestCase;
use PPA\tests\bootstrap\entity\analyser\TestDefaultsEntity;
use PPA\tests\bootstrap\entity\other\ApcuEntity;
use stdClass;

/**
 * @group cache
 */
class APCuTest extends TestCase
{
    /**
     * Is needed for testStoreVsAdd.
     * 
     * @var ApcuEntity
     */
    private static $entity;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        
        
        self::$entity = new ApcuEntity("test");
        
        apcu_add("testStoreVsAdd", self::$entity);
    }
    
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        
        apcu_clear_cache();
    }
    
    public function provideScalars(): array
    {
        return [
            ["int", 10],
            ["string", "hundert"],
            ["float", 2.5],
            ["bool", true]
        ];
    }
    
    /**
     * @dataProvider provideScalars
     */
    public function testAddScalars(string $key, $value): void
    {
        $result = apcu_add($key, $value);
        
        $this->assertTrue($result);
    }
    
    public function testAddAnonymous(): void
    {
        $this->expectExceptionMessage("Serialization of 'class@anonymous' is not allowed");
        
        $anonymous = new class() extends stdClass { private $property = "value"; };
        
        apcu_add("anonymous", $anonymous);
    }
    
    public function provideObjects(): array
    {
        return [
            ["ApcuEntity", new ApcuEntity("provideObjects")],
            ["test", new TestDefaultsEntity()],
        ];
    }
    
    /**
     * @dataProvider provideObjects
     */
    public function testAddObjects(string $key, object $value): void
    {
        $result = apcu_add($key, $value);
        
        $this->assertTrue($result);
    }
    
    /**
     * @dataProvider provideObjects
     */
    public function testFetchObjects(string $key, object $value): void
    {
        $result = apcu_fetch($key);
        
        $this->assertNotFalse($result);
        $this->assertEquals($value, $result);
    }
    
    public function testStoreVsAdd()
    {
        // add doesn't override value
        apcu_add("testStoreVsAdd", 100);
        
        $entity1 = apcu_fetch("testStoreVsAdd");
        
        $this->assertEquals($entity1, self::$entity);
        
        // but store does override value
        apcu_store("testStoreVsAdd", 10);
        
        $entity2 = apcu_fetch("testStoreVsAdd");
        
        $this->assertNotEquals($entity2, self::$entity);
        $this->assertEquals(10, $entity2);
    }
    
    public function testObjectReferences()
    {
        $entity = new ApcuEntity("originalValue");
        
        apcu_add("testObjectReferences", $entity);
        
        // set a new value
        $entity->setTest("newValue");
        
        $entity1 = apcu_fetch("testObjectReferences");
        // we expect the old value, because we did not change object in cache
        $this->assertEquals($entity1->getTest(), "originalValue");
        
        apcu_store("testObjectReferences", $entity);
        
        $entity2 = apcu_fetch("testObjectReferences");
        // we expect the new value, because cache key was overriden
        $this->assertEquals($entity2->getTest(), "newValue");
    }
    
}

?>
