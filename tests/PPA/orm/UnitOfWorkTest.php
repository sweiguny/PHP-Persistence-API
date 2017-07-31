<?php

namespace PPA\tests\orm;

use PHPUnit\Framework\TestCase;
use PPA\core\EventDispatcher;
use PPA\dbal\TransactionManager;
use PPA\orm\EntityManager;
use PPA\tests\bootstrap\entity\Order;
use PPA\tests\bootstrap\TestDriverManager;

class UnitOfWorkTest extends TestCase
{
    
    public function testAddEntity()
    {
        $connection = TestDriverManager::getConnectionByGlobals();
        $connection->connect();
        
        $eventDispatcher    = new EventDispatcher();
        $transactionManager = new TransactionManager($connection);
        $entityManager      = new EntityManager($transactionManager, $eventDispatcher);
        
        $entityManager->persist(new Order());
    }
    
}

?>
