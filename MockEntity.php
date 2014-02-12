<?php

namespace PPA;

use BadFunctionCallException;
use PPA\sql\Query;

class MockEntity extends Entity {

    protected $classname;
    protected $value;
    protected $owner;
    protected $property;

    function __construct($classname, $value, Entity $owner, PersistenceProperty $property) {
        $this->classname = $classname;
        $this->value     = $value;
        $this->owner     = $owner;
        $this->property  = $property;
    }

    
    public function __call($name, $arguments) {
        
        $id    = EntityMetaDataMap::getInstance()->getPrimaryProperty($this->classname);
        $table = EntityMetaDataMap::getInstance()->getTableName($this->classname);

        $query = "SELECT * FROM `{$table}` WHERE {$id->getColumn()} = {$this->value}";
        $q = new Query($query);
        
        $entity = $q->getSingeResult($this->classname);
//        prettyDump($entity);
        echo "*********";
        
        $this->property->setValue($this->owner, $entity);
        
        if (method_exists($entity, $name)) {
            return call_user_func(array($entity, $name), $arguments);
        } else {
            throw new BadFunctionCallException("Method '{$name}()' does not exist in class '{$this->classname}'.");
        }
    }
    
//    public function __get($name) {
//        echo $name;
//    }

}

?>
