<?php

namespace PPA\tests\orm;

use PHPUnit\Framework\TestCase;
use PPA\core\EventDispatcher;
use PPA\core\exceptions\logic\AlreadyExistentInIdentityMapException;
use PPA\dbal\TransactionManager;
use PPA\orm\entity\Serializable;
use PPA\orm\EntityManager;
use PPA\orm\UnitOfWork;
use PPA\tests\bootstrap\entity\Customer;
use PPA\tests\bootstrap\TestDriverManager;
use ReflectionClass;

/**
 * @coversDefaultClass PPA\orm\UnitOfWork
 */
class UnitOfWorkTest extends TestCase
{
    private static $entityManager;
    private static $unitOfWork;
    private static $identityMap;

    public static function setUpBeforeClass()
    {
        $connection = TestDriverManager::getConnectionByGlobals();
        $connection->connect();
        
        $eventDispatcher    = new EventDispatcher();
        $transactionManager = new TransactionManager($connection);
        
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
     * @dataProvider provideEntities
     */
    public function testAddEntity(Serializable $entity, ?string $expectedException)
    {
        if ($expectedException != null)
        {
            $this->expectException($expectedException);
        }
        
//        $connection = TestDriverManager::getConnectionByGlobals();
//        $connection->connect();
        
//        $eventDispatcher    = new EventDispatcher();
//        $transactionManager = new TransactionManager($connection);
//        $entityManager      = new EntityManager($transactionManager, $eventDispatcher);
        
//        $reflector = new ReflectionClass($entityManager);
//        
//        $reflectionProperty = $reflector->getProperty("unitOfWork");
//        $reflectionProperty->setAccessible(true);
        
        /* @var $unitOfWork UnitOfWork */
//        $unitOfWork  = $reflectionProperty->getValue($entityManager);
//        $identityMap = $unitOfWork->getIdentityMap();
        
//        print_r(self::$identityMap);

        self::$entityManager->persist($entity);
        
//        print_r(self::$identityMap);
        
        $result = self::$identityMap->retrieve(get_class($entity), $entity->getCustomerNo());
        
        $this->assertTrue($result === $entity);
        
    }
    
    public function provideEntities()
    {
        return [
            [new Customer(1, "John", "Doe", "at home"), null],
            [new Customer(1, "John", "Doe", "at home"), AlreadyExistentInIdentityMapException::class]
        ];
    }
    
}

?>
