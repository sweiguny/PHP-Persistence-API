<?php

namespace PPA\dbal\query\builder\AST;

use ArrayAccess;
use Countable;
use Iterator;

class SelectList implements ArrayAccess, Countable, Iterator
{
    private $list = [];
    
    public function __construct(Source ...$list)
    {
//        var_dump($list);
        $this->list = $list;
    }
    
    public function getList()
    {
        return $this->list;
    }

    public function addSourceToList(Source $source)
    {
        $this->list[] = $source;
    }

    public function count(): int
    {
        return count($this->list);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->list[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->list[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->list[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->list[$offset]);
    }

    public function current()
    {
        return current($this->list);
    }

    public function key()
    {
        return key($this->list);
    }

    public function next(): void
    {
        next($this->list);
    }

    public function rewind(): void
    {
        reset($this->list);
    }

    public function valid(): bool
    {
        $key = $this->key();
        $var = ($key !== null && $key !== false);
        
        return $var;
    }
}

?>
