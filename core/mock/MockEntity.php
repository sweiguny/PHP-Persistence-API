<?php

namespace PPA\core\mock;

use BadMethodCallException;
use PPA\core\Entity;
use PPA\core\EntityProperty;
use PPA\core\query\PreparedTypedQuery;


class MockEntity extends Entity {

    /**
     * The query for retrieving the true data.
     * 
     * @var string
     */
    protected $query;
    
    /**
     * The classname of the real object.
     * 
     * @var string
     */
    protected $classname;
    
    /**
     * The owner is the parent Entity on which the relationchips are hanging.
     * 
     * @var Entity
     */
    protected $owner;
    
    /**
     * This object gives the capability to set a certain value to the real
     * Entity.
     * 
     * @var EntityProperty
     */
    protected $property;
    
    /**
     * The values for the prepared query.
     * 
     * @var array
     */
    protected $values;

    
    /**
     * The MockEntity serves as replacement for a real Entity. On method calls
     * to an instantiated MockEntity, it will replace itself with a real entity,
     * regarding the properties that are set.
     * 
     * @param string $query
     * @param string $classname
     * @param Entity $owner
     * @param EntityProperty $property
     * @param array $values
     */
    public function __construct($query, $classname, Entity $owner, EntityProperty $property, array $values) {
        $this->query     = $query;
        $this->classname = $classname;
        $this->owner     = $owner;
        $this->property  = $property;
        $this->values    = $values;
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
     * @throws BadMethodCallException If method does not exist.
     */
    public function __call($name, $arguments) {
        $entity = $this->exchange();
        
        if (method_exists($entity, $name)) {
            return call_user_func(array($entity, $name), $arguments);
        } else {
            throw new BadMethodCallException("Method '{$name}()' does not exist in class '" . get_class($entity) . "'.");
        }
    }

    /**
     * @return Entity The true entity instead of the mock.
     */
    protected function exchange() {
        $query  = new PreparedTypedQuery($this->query, $this->classname);
        $entity = $query->getSingleResult($this->values);
        
        $this->property->setValue($this->owner, $entity);
        
        return $entity;
    }

}

?>
