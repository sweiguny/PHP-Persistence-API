<?php

namespace PPA\core\mock;

use ArrayAccess;
use BadMethodCallException;
use Countable;
use PPA\core\Entity;
use PPA\core\EntityProperty;
use PPA\core\query\TypedQuery;


class MockEntityList extends MockEntity implements ArrayAccess, Countable {

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
    public function __construct(TypedQuery $query, Entity $owner, EntityProperty $property) {
        parent::__construct($query, $owner, $property);
    }

    /**
     * It is necessary to find out, if this method is needed!!
     * 
     * @param string $name
     * @param array $arguments
     * @return mixed The value, the entity method should return.
     * @throws BadMethodCallException If method does not exist.
     */
    public function __call($name, $arguments) {
        $entities = $this->exchange();
        
        if (method_exists($entities, $name)) {
            return call_user_func(array($entities, $name), $arguments);
        } else {
            throw new BadMethodCallException("Method '{$name}()' does not exist in class '" . get_class($entities) . "'.");
        }
    }

    protected function exchange() {
        $entities = $this->query->getResultList();
        $this->property->setValue($this->owner, $entities);
        
        return $entities;
    }


    public function offsetExists($offset) {
        return isset($this->exchange()[$offset]);
    }

    public function offsetGet($offset) {
        return $this->exchange()[$offset];
    }

    public function offsetSet($offset, $value) {
        throw new BadMethodCallException("Not possible.");
    }

    public function offsetUnset($offset) {
        throw new BadMethodCallException("Not possible.");
    }

    public function count() {
        return count($this->exchange());
    }

}

?>
