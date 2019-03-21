<?php

namespace PPA\dbal\query\builder\AST;

use PPA\core\exceptions\ExceptionFactory;
use PPA\core\util\StacktraceAnalyzer;
use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\operators\Parenthesis;
use PPA\dbal\query\builder\AST\statements\DQL\SelectStatement;

abstract class ASTNode
{
    /**
     *
     * @var bool
     */
    private $needsDriver = false;
    
    /**
     *
     * @var DriverInterface
     */
    private $driver;
    
    public function __construct(bool $needsDriver)
    {
        $this->needsDriver = $needsDriver;
    }
    
    public abstract function toString(): string;
    
    public function needsDriver(): bool
    {
        return $this->needsDriver;
    }
    
    public function hasDriver(): bool
    {
        return null != $this->driver;
    }
    
    protected function getDriver(): DriverInterface
    {
        if (!$this->hasDriver())
        {
            throw ExceptionFactory::NoDriver($this, new StacktraceAnalyzer());
        }
        
        return $this->driver;
    }

    public function injectDriver(DriverInterface $driver)
    {
        if ($this->hasDriver())
        {
            throw ExceptionFactory::HasDriver($this, new StacktraceAnalyzer());
        }
        
        $this->driver = $driver;
    }
    
    protected final function injectDriversWhereNecessary(self ...$nodes)
    {
        if (!$this->hasDriver())
        {
            throw ExceptionFactory::NoDriver($this, new StacktraceAnalyzer());
        }
        
        for ($i = 0, $count = count($nodes); $i < $count; $i++)
        {
            /* @var $node self */
            $node = $nodes[$i];
            
            if ($node->needsDriver() && !$node->hasDriver())
            {
                $node->injectDriver($this->driver);
            }
        }
    }
    
    protected static final function consolidateNodes(string $glue, ASTNode ...$nodes): ASTNode
    {
        for ($i = 0, $count = count($nodes), $strings = []; $i < $count; $i++)
        {
            /* @var $node ASTNode */
            $node = $nodes[$i];
            
            if ($node instanceof SelectStatement) // Subselect
            {
                $strings[] = Parenthesis::OPEN . $node->toString() . Parenthesis::CLOSE;
            }
            else
            {
                $strings[] = $node->toString();
            }
        }
        
        $wrapper = new class($glue, $strings) extends ASTNode
        {
            /**
             *
             * @var array
             */
            private $strings;
            
            /**
             *
             * @var string
             */
            private $glue;

            public function __construct(string $glue, array $strings)
            {
                parent::__construct(false);
                
                $this->strings = $strings;
                $this->glue    = $glue;
            }
            
            public function toString(): string
            {
                return trim(implode($this->glue, $this->strings));
            }
        };
        
        return $wrapper;
    }
    
}

?>
