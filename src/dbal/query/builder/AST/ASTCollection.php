<?php

namespace PPA\dbal\query\builder\AST;

use ArrayAccess;
use Countable;
use Exception;
use Iterator;
use PPA\dbal\query\builder\StatementState;

class ASTCollection implements ArrayAccess, Countable, Iterator, SQLElementInterface
{
//    const STATE_DIRTY = -1;
//    const STATE_CLEAN = 1;
    
    /**
     *
     * @var int
     */
    private $state;// = self::STATE_CLEAN;
    
    /**
     *
     * @var array of SQLElementInterface
     */
    protected $collection = [];
    
    public function __construct()
    {
        $this->state = new StatementState();
    }
    
    public function count(): int
    {
        return count($this->collection);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->collection[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->collection[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        if (empty($offset))
        {
            $this->collection[] = $value;
        }
        else
        {
            $this->collection[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->collection[$offset]);
    }

    public function current()
    {
        return current($this->collection);
    }

    public function key()
    {
        return key($this->collection);
    }

    public function next(): void
    {
        next($this->collection);
    }

    public function rewind(): void
    {
        reset($this->collection);
    }

    public function valid(): bool
    {
        $key = $this->key();
        $var = ($key !== null && $key !== false);
        
        return $var;
    }

    public function toString(): string
    {
        if ($this->getState()->stateIsDirty())
        {
            throw new Exception("State of class '" . get_class($this) . "' is dirty. Reason: " . $this->getState()->getReason());
        }
        
        $collection = $this->collection;
        $strings    = [];
//        var_dump(array_keys($collection));die();
        
        for ($i = 0, $count = count($collection); $i < $count; $i++)
        {
            $element = $collection[$i];
            
//            if (!(is_string($element) && empty($element)))
//            if ($element != "")
            {
                $strings[] = $this->workOnElement($element);
            }
        }
        
//        var_dump($collection);
//        var_dump($this->collection);
//        var_dump($strings);
        
        return implode(" ", $strings);
    }
    
    protected function workOnElement(SQLElementInterface $element): string
    {
        return trim($element->toString());
    }
    
    public function getState(): StatementState
    {
        return $this->state;
    }

}

?>
