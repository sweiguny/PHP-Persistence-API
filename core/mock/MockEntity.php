<?php

namespace PPA;

use BadFunctionCallException;
use PPA\sql\Query;

class MockEntity extends Entity {

    protected $classname;
    protected $value;
    protected $owner;
    protected $property;

    /**
     * The MockEntity serves as replacement for a real Entity. On method calls
     * to an instantiated MockEntity, it will replace itself with a real entity,
     * regarding the properties that are set.
     * 
     * @param string $classname
     * @param mixed $value
     * @param \PPA\Entity $owner
     * @param \PPA\PersistenceProperty $property
     */
    public function __construct($classname, $value, Entity $owner, PersistenceProperty $property) {
        $this->classname = $classname;
        $this->value     = $value;
        $this->owner     = $owner;
        $this->property  = $property;
    }

    /**
     * Fetches the entity from the database, injects it into the owner and calls
     * the specific function of the entity.
     * 
     * This method does only apply regarding lazy loading.
     * 
     * @param string $name
     * @param array $arguments
     * @return mixed The value, the entity method should return.
     * @throws BadFunctionCallException If method does not exist.
     */
    public function __call($name, $arguments) {
        $id    = EntityMetaDataMap::getInstance()->getPrimaryProperty($this->classname);
        $table = EntityMetaDataMap::getInstance()->getTableName($this->classname);

        $query  = new Query("SELECT * FROM `{$table}` WHERE {$id->getColumn()} = {$this->value}");
        $entity = $query->getSingeResult($this->classname);
        
        $this->property->setValue($this->owner, $entity);
        
        if (method_exists($entity, $name)) {
            return call_user_func(array($entity, $name), $arguments);
        } else {
            throw new BadFunctionCallException("Method '{$name}()' does not exist in class '{$this->classname}'.");
        }
    }

}

?>
