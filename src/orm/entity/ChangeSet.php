<?php

namespace PPA\orm\entity;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * Description of ChangeSet
 *
 * @author siwe
 */
class ChangeSet implements ArrayAccess , Countable, Iterator
{
    
    private $changes = [];
    
    public function getChanges()
    {
        return $this->changes;
    }

    public function addChange(Change $change)
    {
        $this->changes[] = $change;
    }

    public function count(): int
    {
        return count($this->changes);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->changes[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->changes[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->changes[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->changes[$offset]);
    }

    public function current()
    {
        return current($this->changes);
    }

    public function key(): scalar
    {
        return key($this->changes);
    }

    public function next(): void
    {
        next($this->changes);
    }

    public function rewind(): void
    {
        reset($this->changes);
    }

    public function valid(): bool
    {
        $key = $this->key();
        $var = ($key !== null && $key !== false);
        
        return $var;
    }

}

?>
