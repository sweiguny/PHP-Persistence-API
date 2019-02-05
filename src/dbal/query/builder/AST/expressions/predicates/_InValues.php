<?php

namespace PPA\dbal\query\builder\AST\expressions\predicates;

use PPA\core\exceptions\ExceptionFactory;
use PPA\dbal\query\builder\AST\expressions\_Literal;
use PPA\dbal\query\builder\AST\expressions\Expression;

class _InValues extends Predicate
{
    /**
     *
     * @var array
     */
    private $values;
    
    /**
     *
     * @var Expression
     */
    private $left;
    
    /**
     *
     * @var array
     */
    private $right;
    
    public function __construct(Expression $left, array $right)
    {
        parent::__construct();
        
        $this->left  = $left;
        $this->right = $right;
    }
    
    public function toString(): string
    {
        $this->makeParametersToNodes();
        $this->injectDriversWhereNecessary($this->left, ...$this->values);
        $consolidated = self::consolidateNodes(", ", ...$this->values);
        
        return $this->left->toString() . " IN(" . $consolidated->toString() . ")";
    }
    
    private function makeParametersToNodes()
    {
        for ($i = 0, $cnt = count($this->right); $i < $cnt; $i++)
        {
            $value = $this->right[$i];
            
            if (is_scalar($value))
            {
                $this->values[] = new _Literal($value, gettype($value));
            }
            else if ($value instanceof Expression)
            {
                $this->values[] = $value;
            }
            else
            {
                throw ExceptionFactory::InvalidArgument("The type '" . gettype($value) . "' is not allowed for the IN predicate.");
            }
        }
    }
}

?>
