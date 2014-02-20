<?php

namespace PPA\core\mock;

use BadMethodCallException;
use PPA\core\Entity;
use PPA\core\EntityProperty;
use PPA\core\query\PreparedTypedQuery;


class MockEntity extends Entity {

    protected $owner;
    protected $property;
    protected $query;
    protected $values;

    /**
     * The MockEntity serves as replacement for a real Entity. On method calls
     * to an instantiated MockEntity, it will replace itself with a real entity,
     * regarding the properties that are set.
     * 
     * @param string $classname
     * @param mixed $value
     * @param Entity $owner
     * @param EntityProperty $property
     */
    public function __construct(PreparedTypedQuery $query, Entity $owner, EntityProperty $property, array $values) {
        $this->query    = $query;
        $this->owner    = $owner;
        $this->property = $property;
        $this->values   = $values;
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
        $entity = $this->query->getSingleResult($this->values);
        $this->property->setValue($this->owner, $entity);
        
        return $entity;
    }

}

?>
