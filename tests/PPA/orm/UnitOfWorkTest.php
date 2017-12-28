<?php

namespace PPA\tests\orm;

use PHPUnit\Framework\TestCase;
use PPA\core\EventDispatcher;
use PPA\core\exceptions\logic\AlreadyExistentInIdentityMapException;
use PPA\core\exceptions\logic\NotExistentInIdentityMapException;
use PPA\dbal\TransactionManager;
use PPA\orm\entity\Serializable;
use PPA\orm\EntityManager;
use PPA\orm\event\entityManagement\FlushEvent;
use PPA\orm\IdentityMap;
use PPA\orm\OriginsMap;
use PPA\orm\UnitOfWork;
use PPA\tests\bootstrap\entity\Customer;
use PPA\tests\bootstrap\TestDriverManager;
use ReflectionClass;

/**
 * @coversDefaultClass PPA\orm\UnitOfWork
 */
class UnitOfWorkTest extends TestCase
{
    /**
     *
     * @var EntityManager
     */
    private static $entityManager;
    
    /**
     *
     * @var UnitOfWork
     */
    private static $unitOfWork;
    
    /**
     *
     * @var IdentityMap
     */
    private static $identityMap;
    
    /**
     *
     * @var OriginsMap
     */
    private static $originsMap;

    public static function setUpBeforeClass(): void
    {
        $connection = TestDriverManager::getConnectionByGlobals();
        $connection->connect();
        
        $eventDispatcher    = new EventDispatcher();
        $transactionManager = new TransactionManager($connection, $eventDispatcher);
        
        self::$entityManager = new EntityManager($transactionManager, $eventDispatcher);
        
        $reflector = new ReflectionClass(self::$entityManager);
        
        $reflectionProperty = $reflector->getProperty("unitOfWork");
        $reflectionProperty->setAccessible(true);
        
        self::$unitOfWork  = $reflectionProperty->getValue(self::$entityManager);
        self::$identityMap = self::$unitOfWork->getIdentityMap();
        self::$originsMap  = self::$unitOfWork->getOriginsMap();
    }
    
    /**
     * @covers ::addEntity
     * @covers PPA\orm\IdentityMap::add
     * @covers PPA\orm\IdentityMap::retrieve
     * @covers PPA\orm\OriginsMap::add
     * @covers PPA\orm\OriginsMap::retrieve
     * 
     * @dataProvider provideEntitiesToAdd
     */
    public function testAddEntity(Serializable $entity, string $expectedException = null): void
    {
        if ($expectedException != null)
        {
            $this->expectException($expectedException);
        }

        self::$entityManager->persist($entity);
        
        $result1 = self::$identityMap->retrieve(get_class($entity), $entity->getCustomerNo());
        $result2 = self::$originsMap->retrieve(get_class($entity), $entity->getCustomerNo());
        
        $this->assertTrue($result1 === $entity);
        $this->assertNotNull($result2);
    }
    
    /**
     * @covers ::removeEntity
     * @covers PPA\orm\IdentityMap::remove
     * @covers PPA\orm\IdentityMap::retrieve
     * @covers PPA\orm\OriginsMap::remove
     * @covers PPA\orm\OriginsMap::retrieve
     * 
     * @dataProvider provideEntitiesToRemove
     */
    public function testRemoveEntity(Serializable $entity, string $expectedException = null): void
    {
        if ($expectedException != null)
        {
            $this->expectException($expectedException);
        }
        
        self::$entityManager->remove($entity);
        
        $result1 = self::$identityMap->retrieve(get_class($entity), $entity->getCustomerNo());
        $result2 = self::$originsMap->retrieve(get_class($entity), $entity->getCustomerNo());
        
        $this->assertNull($result1);
        $this->assertNull($result2);
    }
    
    public function provideEntitiesToAdd(): array
    {
        $reflectionClass   = new ReflectionClass(Customer::class);
        $constructorParams = [1, "John", "Doe", "at home"];
        $testEntity        = $reflectionClass->newInstanceArgs($constructorParams);
        
        
        return [
            [$testEntity],
            [$testEntity, AlreadyExistentInIdentityMapException::class]
        ];
    }
    
    public function provideEntitiesToRemove(): array
    {
        $reflectionClass   = new ReflectionClass(Customer::class);
        $constructorParams = [1, "John", "Doe", "at home"];
        $testEntity        = $reflectionClass->newInstanceArgs($constructorParams);
        
        
        return [
            [$testEntity],
            [$testEntity, NotExistentInIdentityMapException::class]
        ];
    }
    
    
    /**
     * @covers ::getChangeSet
     * @covers PPA\orm\OriginsMap::extractData
     * @covers PPA\orm\OriginsMap::retrieve
     * 
     * @depends testAddEntity
     */
    public function testGetChangeset(): Serializable
    {
        $entity = new Customer(2, "Jane", "Doe", "over there");
        
        self::$entityManager->persist($entity);
        
        $entity->setFirstname("MyFirstName");
        
        $result = self::$unitOfWork->getChangeSet($entity);
        
        $this->assertNotEmpty($result);
        $this->assertEquals(1, count($result));
        
        return $entity;
    }
    
    /**
     * @covers ::writeChanges
     * 
     * @depends testGetChangeset
     */
    public function testWriteChanges(Customer $entity): void
    {
        $reflector = new ReflectionClass(self::$unitOfWork);
        
        $writeChanges = $reflector->getMethod("writeChanges");
        $writeChanges->setAccessible(true);
        $numStmts = $writeChanges->invoke(self::$unitOfWork, new FlushEvent(self::$entityManager, $entity));
        
        $this->assertEquals(1, $numStmts);
    }
    
    
    
}

?>
