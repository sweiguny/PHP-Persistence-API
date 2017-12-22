<?php

namespace PPA\tests\orm;

use PHPUnit\Framework\TestCase;
use PPA\core\EventDispatcher;
use PPA\core\exceptions\logic\AlreadyExistentInIdentityMapException;
use PPA\core\exceptions\logic\NotExistentInIdentityMapException;
use PPA\dbal\TransactionManager;
use PPA\orm\entity\Serializable;
use PPA\orm\EntityManager;
use PPA\orm\IdentityMap;
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

    public static function setUpBeforeClass()
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
    }
    
    /**
     * @covers ::addEntity
     * @covers PPA\orm\IdentityMap::add
     * @covers PPA\orm\IdentityMap::retrieve
     * 
     * @dataProvider provideEntitiesToAdd
     */
    public function testAddEntity(Serializable $entity, string $expectedException = null)
    {
        if ($expectedException != null)
        {
            $this->expectException($expectedException);
        }

        self::$entityManager->persist($entity);
        
        $result = self::$identityMap->retrieve(get_class($entity), $entity->getCustomerNo());
        
        $this->assertTrue($result === $entity);
    }
    
    /**
     * @covers ::removeEntity
     * @covers PPA\orm\IdentityMap::remove
     * @covers PPA\orm\IdentityMap::retrieve
     * 
     * @depends testAddEntity
     * @dataProvider provideEntitiesToRemove
     */
    public function testRemoveEntity(Serializable $entity, string $expectedException = null)
    {
        if ($expectedException != null)
        {
            $this->expectException($expectedException);
        }
        
        self::$entityManager->remove($entity);
        
        $result = self::$identityMap->retrieve(get_class($entity), $entity->getCustomerNo());
        
        $this->assertNull($result);
    }
    
    public function provideEntitiesToAdd()
    {
        return [
            [new Customer(1, "John", "Doe", "at home")],
            [new Customer(1, "John", "Doe", "at home"), AlreadyExistentInIdentityMapException::class]
        ];
    }
    
    public function provideEntitiesToRemove()
    {
        return [
            [new Customer(1, "John", "Doe", "at home")],
            [new Customer(1, "John", "Doe", "at home"), NotExistentInIdentityMapException::class]
        ];
    }
    
}

?>
