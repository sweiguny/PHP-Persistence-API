<?php

namespace PPA\tests\other;

use PHPUnit\Framework\TestCase;
use PPA\tests\bootstrap\entity\City;
use PPA\tests\bootstrap\entity\TestDefaultsEntity;
use stdClass;

/**
 * @group cache
 */
class APCuTest extends TestCase
{
    /**
     * Is needed for testStoreVsAdd.
     * 
     * @var City
     */
    private static $city;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        
        
        self::$city = new City("Oftering", 4064, 1);
        
        apcu_add("testStoreVsAdd", self::$city);
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
            ["city", new City("Linz", 4020, 123)],
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
        
        $city1 = apcu_fetch("testStoreVsAdd");
        
        $this->assertEquals($city1, self::$city);
        
        // but store does override value
        apcu_store("testStoreVsAdd", 10);
        
        $city2 = apcu_fetch("testStoreVsAdd");
        
        $this->assertNotEquals($city2, self::$city);
        $this->assertEquals(10, $city2);
    }
    
    public function testObjectReferences()
    {
        $city = new City("Oftering", 4064, 1);
        
        apcu_add("testObjectReferences", $city);
        
        // set a new value
        $city->setName("Kirchstetten");
        
        $city1 = apcu_fetch("testObjectReferences");
        // we expect the old value, because we did not change object in cache
        $this->assertEquals($city1->getName(), "Oftering");
        
        apcu_store("testObjectReferences", $city);
        
        $city2 = apcu_fetch("testObjectReferences");
        // we expect the new value, because cache key was overriden
        $this->assertEquals($city2->getName(), "Kirchstetten");
    }
    
}

?>
