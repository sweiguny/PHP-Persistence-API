<?php

namespace PPA\dbal\query\builder\AST;

use ArrayAccess;
use PPA\core\exceptions\ExceptionFactory;
use PPA\dbal\drivers\DriverInterface;

class ASTCollection extends ASTNode implements ArrayAccess
{
    /**
     *
     * @var array
     */
    private $collection = [];
    
    public function __construct(DriverInterface $driver = null)
    {
        // TODO: Maybe it is not always wanted/possible to inject the driver through constructor?
        // 01.02.2019: If some releases didn't need it, this comment can be removed.
        // 02.02.2019: Found that class Predicate should use an ASTCollection, but since it is used in AST_delegates.php, driver can only be set lazily...
        
        parent::__construct(true);
        
        if ($driver != null)
        {
            $this->injectDriver($driver);
        }
    }

    public function toString(): string
    {
        // TODO: Maybe it is necessary to call $this->injectDriversWhereNecessary();
        if ($this->hasDriver())
        {
            $this->injectDriversWhereNecessary(...$this->collection);
        }
        
        for ($i = 0, $string = [], $count = count($this->collection); $i < $count; $i++)
        {
            /* @var $node ASTNode */
            $node = $this->collection[$i];
            
            // It shouldn't be necessary to trim()...
//            $strings[] = trim($node->toString());
            $string[] = $node->toString();
        }
        
        return trim(implode(" ", $string));
    }
    
    private function checkOffset($offset)
    {
        if (!is_integer($offset))
        {
            throw ExceptionFactory::TypeError(sprintf("Offset for array-access-calls of '%s' must be from type '%s', but was '%s'.", get_class($this), "integer", gettype($offset)));
        }
    }
    
    private function checkValue($value)
    {
        if (!($value instanceof ASTNode))
        {
            throw ExceptionFactory::TypeError(sprintf("Value to add to '%s' must be from type '%s', but was '%s'.", get_class($this), ASTNode::class, is_object($value) ? get_class($value) : gettype($value)));
        }
    }

    public function offsetExists($offset): bool
    {
        $this->checkOffset($offset);
        return isset($this->collection[$offset]);
    }

    public function offsetGet($offset)
    {
        $this->checkOffset($offset);
        return $this->collection[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->checkValue($value);
        
        if (is_null($offset))
        {
            $this->collection[] = $value;
        }
        else
        {
            $this->checkOffset($offset);
            $this->collection[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        $this->checkOffset($offset);
        unset($this->collection[$offset]);
    }

}

?>
