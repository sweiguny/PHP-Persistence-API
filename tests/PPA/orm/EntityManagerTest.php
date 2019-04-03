<?php

namespace PPA\tests\orm;

use Mockery;
use PHPUnit\Framework\TestCase;
use PPA\core\EventDispatcher;
use PPA\dbal\Connection;
use PPA\dbal\TransactionManager;
use PPA\orm\EntityManager;
use PPA\tests\bootstrap\entity\Customer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EntityManagerTest extends TestCase
{
    /**
     *
     * @var EntityManager
     */
    private static $entityManager;

    public static function setUpBeforeClass(): void
    {
        $connection = Mockery::mock(Connection::class);
        $connection->expects("getPdo");
        
        $eventDispatcher    = new EventDispatcher();
        $transactionManager = new TransactionManager($connection, $eventDispatcher);
        
        self::$entityManager = new EntityManager($transactionManager, $eventDispatcher);
        
    }
    
    public function testX()
    {
        $entity = new Customer(1, "John", "Doe", "at home");
        
        self::$entityManager->persist($entity);
    }
    
}

?>
