<?php

namespace PPA\tests\orm;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PPA\core\EventDispatcher;
use PPA\core\exceptions\logic\DomainException;
use PPA\orm\EntityAnalyser;
use PPA\orm\EntityManager;
use PPA\orm\maps\EntityStatesMap;
use PPA\orm\maps\IdentityMap;
use PPA\orm\maps\OriginsMap;
use PPA\orm\UnitOfWork;
use PPA\tests\bootstrap\entity\Customer;
use PPA\tests\bootstrap\entity\em\Entity1;
use ReflectionClass;

class EntityManagerTest extends MockeryTestCase
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

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        
        $eventDispatcher = new EventDispatcher();
        $analyser        = new EntityAnalyser();
        $originsMap      = new OriginsMap();
        $identityMap     = new IdentityMap($analyser);
        
        self::$unitOfWork = new UnitOfWork($eventDispatcher, $analyser, $originsMap, $identityMap);
        
        self::$entityManager = new EntityManager($eventDispatcher, self::$unitOfWork, $analyser);
    }
    
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
    }
    
//    protected function setUp(): void
//    {
//        parent::setUp();
//    }
//    
    protected function tearDown(): void
    {
        parent::tearDown();
        
//        Mockery::close();
    }
    
    public function provideMethodNames(): array
    {
        return [
            ["persist"],
            ["remove"]
        ];
    }
    
    /**
     * @dataProvider provideMethodNames
     */
    public function testInvalidStatesException(string $methodName): void
    {
//        $this->expectException(DomainException::class);
        $exceptionThrown = false;
        
        $reflector = new ReflectionClass(UnitOfWork::class);
        $property  = $reflector->getProperty("entityStatesMap");
        
        $property->setAccessible(true);
        
        // Save original entityStatesMap to be able to inject it after test again.
        $entityStatesMapOrig = $property->getValue(self::$unitOfWork);
        
        // Create mock for entityStatesMap
        $entityStatesMapMock = Mockery::mock(EntityStatesMap::class);
        $entityStatesMapMock->shouldReceive("getStatus")->andReturn(-1);
        
        // Inject mock for entityStatesMap, to be able to make a proper test.
        $property->setValue(self::$unitOfWork, $entityStatesMapMock);
        
        $entity = new Entity1(1);
        
        try
        {
            self::$entityManager->$methodName($entity);
        }
        catch (DomainException $exc)
        {
            $exceptionThrown = true;
        }
        finally
        {
            // Inject origin for entityStatesMap again to have a clean state for other tests.
            $property->setValue(self::$unitOfWork, $entityStatesMapOrig);
        }

        $this->assertTrue($exceptionThrown, "Expected to throw a " . DomainException::class . ".");
    }
    
    
    
    public function testPersistingNewEntity()
    {
        $entity = new Entity1(1);
        
        self::$entityManager->persist($entity);
    }
    
    public function testRetrieveEntityFromIdentityMap()
    {
        // retrieve Entity from Repo
        // modify entity
        // try to retrieve it again from repo
        // there should be no SQL statement, but a request from identitymap
        // 
        
        self::$entityManager->findByPrimary(Entity1::class, [1]);
        self::$entityManager->findByPrimary(Customer::class, [1]);
    }
    
    public function testTransactionalScope()
    {
        // retrieve Entity from Repo
        // modify entity
        // begin transaction
        // another modification
        // rollback
        // second modification should be rolled back in originsmap
    }
    
    
}

?>
