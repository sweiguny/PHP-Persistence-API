<?php

namespace PPA\orm;

use Exception;
use PPA\orm\entity\Change;
use PPA\orm\entity\ChangeSet;
use PPA\orm\entity\Serializable;
use PPA\orm\event\entityManagement\EntityPersistEvent;
use PPA\orm\event\entityManagement\EntityRemoveEvent;
use PPA\orm\event\entityManagement\FlushEvent;
use PPA\orm\mapping\types\TypeString;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UnitOfWork implements EventSubscriberInterface
{
    /**
     *
     * @var EntityManager
     */
    private $entityManager;
    
    /**
     * 
     * @var IdentityMap
     */
    private $identityMap;
    
    /**
     *
     * @var OriginsMap
     */
    private $originsMap;

    /**
     * 
     * @var EntityAnalyser 
     */
    private $analyser;

    /**
     * 
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EntityPersistEvent::NAME => "addEntity",
            EntityRemoveEvent::NAME  => "removeEntity",
            FlushEvent::NAME         => "writeChanges"
        ];
    }
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->analyser      = new EntityAnalyser();
        $this->identityMap   = new IdentityMap();
        $this->originsMap    = new OriginsMap($this->analyser);
    }
    
    public function getChangeSet(Serializable $entity): ChangeSet
    {
        $changeSet    = new ChangeSet();
        $analysis     = $this->analyser->getMetaData($entity);
        $properties   = $analysis->getPropertiesByName();
        $originalData = $this->originsMap->retrieve($analysis->getClassname(), $analysis->getPrimaryProperty()->getValue($entity));
        $currentData  = $this->originsMap->extractData($entity);
        
        $consistencyCheck1 = array_diff_key($originalData, $currentData);
        $consistencyCheck2 = array_diff_key($currentData, $originalData);
        
        if (!empty($consistencyCheck1) || !empty($consistencyCheck2))
        {
            throw new Exception("Somethings wrong here!");
        }
        
        foreach ($currentData as $propertyName => $value)
        {
            $originalValue = $originalData[$propertyName];
            
            if ($value != $originalValue)
            {
                $changeSet[] = new Change($properties[$propertyName], $originalValue, $value);
//                $changeSet->addChange(
//                        new Change($propertyName, $originalValue, $value)
//                    );
            }
        }
        
        return $changeSet;
    }
    
    protected function writeChanges(FlushEvent $event)
    {
        $managedEntities = $this->identityMap->dumpMapByObjectId();
        
        foreach ($managedEntities as $oid => $entity)
        {
            /* @var $entity Serializable */
            
            $analysis  = $this->analyser->getMetaData($entity);
            $changeSet = $this->getChangeSet($entity);
            
            if (!empty($changeSet))
            {
                $query = "UPDATE `{$analysis->getTableName()}` SET ";
                
                foreach ($changeSet as $change)
                {
                    /* @var $change Change */
                    
                    $column   = $change->getProperty()->getColumn();
                    $dataType = $column->getDatatype();
                    
                    if (get_class($dataType) == TypeString::class)
                    {
                        $value = "'{$change->getToValue()}'";
                    }
                    else
                    {
                        $value = $change->getToValue();
                    }
                    
                    $query .= "`{$column->getName()}` = {$value}";
                }
                
                var_dump($query);
            }
            
        }
        
        die();
    }

    public function addEntity(EntityPersistEvent $event)
    {
        $entityManager = $event->getEntityManager();
        $entity        = $event->getEntity();
        
        $metaData = $this->analyser->getMetaData($entity);
        
        $key = $metaData->getPrimaryProperty()->getValue($entity);
        
        $this->identityMap->add($entity, $key);
        $this->originsMap->add($entity, $key);
    }

    public function removeEntity(EntityRemoveEvent $event)
    {
        $entityManager = $event->getEntityManager();
        $entity        = $event->getEntity();
        
        $metaData = $this->analyser->getMetaData($entity);
        
        $key = $metaData->getPrimaryProperty()->getValue($entity);
        
        $this->identityMap->remove($entity, $key);
        $this->originsMap->remove($entity, $key);
    }

    public function getIdentityMap(): IdentityMap
    {
        return $this->identityMap;
    }
    
    public function getOriginsMap(): OriginsMap
    {
        return $this->originsMap;
    }
    
}

?>
