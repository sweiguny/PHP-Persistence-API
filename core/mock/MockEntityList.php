<?php

namespace PPA\core\mock;

use ArrayAccess;
use BadMethodCallException;
use Countable;
use Iterator;
use PPA\core\Entity;
use PPA\core\EntityProperty;
use PPA\core\query\PreparedTypedQuery;
use PPA\PPA;


class MockEntityList extends MockEntity implements ArrayAccess, Countable, Iterator {

    /**
     *
     * @var array
     */
    private $entities;

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
        parent::__construct($query, $classname, $owner, $property, $values);
    }

    /**
     * This method will be called, when no other method applies to $this.
     * 
     * @param string $name
     * @param array $arguments
     * @return mixed The value, the function of the internal array should return.
     * @throws BadMethodCallException If method does not exist.
     */
    public function __call($name, $arguments) {
        $this->exchange();
        
        if (method_exists($this->entities, $name)) {
            return call_user_func(array($this->entities, $name), $arguments);
        } else {
            throw new BadMethodCallException("Method '{$name}()' cannot be called on an Array.");
        }
    }

    /**
     * Exchanges the mock list with an array that contains true entities.
     */
    protected function exchange() {
        if ($this->entities == null) {
            PPA::log(1011, array($this->classname));
            $query          = new PreparedTypedQuery($this->query, $this->classname);
            $this->entities = $query->getResultList($this->values);
            
            $this->property->setValue($this->owner, $this->entities);
        }
    }

    public function offsetExists($offset) {
        $this->exchange();
        return isset($this->entities[$offset]);
    }

    public function offsetGet($offset) {
        $this->exchange();
        return $this->entities[$offset];
    }

    public function offsetSet($offset, $value) {
        $this->exchange();
        
        if ($offset == null) {
            $this->entities[] = $value;
        } else {
            $this->entities[$offset] = $value;
        }
        
        // Must be done twice (first time is in method 'exchange()'), because to
        // '$this->property->setValue(...)' the parameters are passed by value
        // and not by reference. As the entities are changed after the first
        // time, they must be set a second time.
        // Maybe some genius can increase the quality of this explanation. ;)
        $this->property->setValue($this->owner, $this->entities);
    }

    public function offsetUnset($offset) {
        $this->exchange();
        unset($this->entities[$offset]);
    }

    public function count() {
        $this->exchange();
        return count($this->entities);
    }

    public function current() {
        $this->exchange();
        return current($this->entities);
    }

    public function key() {
        $this->exchange();
        return key($this->entities);
    }

    public function next() {
        $this->exchange();
        return next($this->entities);
    }

    public function rewind() {
        $this->exchange();
        reset($this->entities);
    }

    public function valid() {
        $key = $this->key();
        $var = ($key !== null && $key !== false);
        return $var;
    }

}

?>
