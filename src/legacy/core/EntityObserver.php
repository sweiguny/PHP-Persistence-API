<?php

namespace PPA\core;

use Exception;
use PPA\core\changeSet\EntityChange;
use PPA\core\changeSet\EntityChangeSet;
use PPA\core\mock\MockEntity;
use PPA\core\relation\ManyToMany;
use PPA\core\relation\OneToMany;
use PPA\core\relation\OneToOne;

class EntityObserver
{
    private static $entities = [];
    
    public static function registerEntity(HistoryEntity $entity)
    {
        list($classname, $primaryValue) = self::analyze($entity);
        
        
        if (!isset(self::$entities[$classname]))
        {
            self::$entities[$classname] = [];
        }
        
        if (!isset(self::$entities[$classname][$primaryValue]))
        {
            self::$entities[$classname][$primaryValue] = self::getArrayOfEntity($classname, $entity);
        }
//        else
//        {
//            throw new \Exception("Is already registered");
//        }
        
//        \PPA\prettyDump("-----------list entities------------");
//        \PPA\prettyDump(self::$entities);
//        \PPA\prettyDump("-------end of list of entities------");
    }
    
    public static function getChangeSets(HistoryEntity $entity)
    {
        list($classname, $primaryValue) = self::analyze($entity);
        
        $before = self::$entities[$classname][$primaryValue];
        $after  = self::getArrayOfEntity($classname, $entity);
        
        
        if ($before == $after) // Entity has not changed
        {
            return [];
        }
        else // Entity has changed
        {
            return self::compare($classname, $before, $after);
        }
    }
    
    private static function analyze(Entity $entity)
    {
        $classname         = get_class($entity);
        $primaryValue      = EntityMetaDataMap::getInstance()->getPrimaryProperty($classname)->getValue($entity);
        $propertiesToTrack = EntityMetaDataMap::getInstance()->getPropertiesToTrack($classname);
        
        foreach ($propertiesToTrack as $property)
        {
            if ($property->hasRelation())
            {
                if ($property->getRelation()->isOneToOne())
                {
                    $subEntity = $property->getValue($classname);
                    
                    list($subClassname, $subPrimaryValue) = self::analyze($subEntity);
                    
                    self::$entities[$subClassname][$subPrimaryValue] = self::getArrayOfEntity($subClassname, $subEntity);
                }
            }
        }
        
        return [$classname, $primaryValue];
    }
    
    private static function getArrayOfEntity($classname, Entity $entity)
    {
        $propertiesToTrack = EntityMetaDataMap::getInstance()->getPropertiesToTrack($classname);
        $array             = [];
        
        foreach ($propertiesToTrack as $property)
        {
            $array[$property->getName()] = $property->getValue($entity);
        }
        
        return $array;
    }
    
    private static function compare($classname, array $dataBefore, array $dataAfter)
    {
        $propertiesToTrack = EntityMetaDataMap::getInstance()->getPropertiesToTrack($classname);
        
        $changeSets = [
            $classname => new EntityChangeSet()
        ];
        
        foreach ($propertiesToTrack as $property)
        {
            $name   = $property->getName();
            $before = $dataBefore[$name];
            $after  = $dataAfter[$name];
            
            if ($property->hasRelation())
            {
                self::checkRelations($classname, $changeSets, $property, $before, $after);
            }
            else
            {
                if ($before != $after)
                {
                    $changeSets[$classname]->addChange(new EntityChange($name, $before, $after));
                }
            }
            
//                throw new PPA_Exception("States of '{$classname}->{$name}' are not comparable. Before: " . gettype($before) . " After: " . gettype($after));
        }
        
        return $changeSets;
    }

    private static function checkRelations($classname, array &$changeSets, EntityProperty $property, $before, $after)
    {
        $relation = $property->getRelation();
        
        if ($relation instanceof OneToOne)
        {
            self::handleOneToOne($classname, $changeSets, $property->getName(), $relation, $before, $after);
        }
        else if ($relation instanceof OneToMany)
        {
            throw new Exception("Not yet implemented");
        }
        else if ($relation instanceof ManyToMany)
        {
            throw new Exception("Not yet implemented");
        }
    }
    
    private static function handleOneToOne($classname, array &$changeSets, $propertyName, OneToOne $relation, $before, $after)
    {
        if ($after instanceof MockEntity)
        {
//            \PPA\prettyDump("not changed");
        }
        else
        {
            if ($before instanceof MockEntity)
            {
                $before = $before->exchange();
            }

            list($classBefore, $primaryBefore) = self::analyze($before);
            list($classAfter,  $primaryAfter)  = self::analyze($after);

            if ($primaryBefore == $primaryAfter)
            {
                if ($relation->isCascadeTypePersist())
                {
                    if ($after instanceof HistoryEntity)
                    {
                        $additionalChangeSets = self::getChangeSets($after);
                        
                        $changeSets = array_merge($changeSets, $additionalChangeSets);
//                        \PPA\prettyDump("------Additional changeset-------");
//                        \PPA\prettyDump($additionalChangeSets);
//                        \PPA\prettyDump("------end of changeset-----------");
                    }
                    else
                    {
                        // If entity is normal Entity add to this changeset
//                        self::
                    }
                }
            }
            else
            {
                // Use object reference?! Only the id will probably don't be enough
                $changeSets[$classname]->addChange(new EntityChange($propertyName, $primaryBefore, $primaryAfter));
            }
        }
    }
    
}

?>