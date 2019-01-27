<?php

namespace PPA\dbal\query\builder\AST;

use ArrayAccess;
use Countable;
use Iterator;
use PPA\core\exceptions\ExceptionFactory;
use PPA\dbal\query\builder\StatementState;

/**
 * Abstract Syntax Tree
 */
class ASTCollection implements SQLElementInterface
{
    /**
     *
     * @var int
     */
    private $state;
    
    /**
     *
     * @var array of SQLElementInterface
     */
    protected $collection = [];
    
    public function __construct()
    {
        $this->state = new StatementState();
    }

    public function toString(): string
    {
        if ($this->getState()->stateIsDirty())
        {
            throw ExceptionFactory::CollectionState($this->getState()->getCode(), "State of class '" . get_class($this) . "' is dirty. Reason: " . $this->getState()->getReason());
        }
        
        $collection = $this->collection;
        $strings    = [];
        
        for ($i = 0, $count = count($collection); $i < $count; $i++)
        {
            $element = $collection[$i];
            $string  = $this->workOnElement($element);
            
            if ($string != "")
            {
                $strings[] = $string;
            }
        }
        
//        print_r("---------------------\n");
//        print_r(array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 0, 4));
//        print_r($collection);
//        print_r($strings);
        
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
