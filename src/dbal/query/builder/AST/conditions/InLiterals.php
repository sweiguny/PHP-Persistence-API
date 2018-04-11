<?php

namespace PPA\dbal\query\builder\AST\conditions;

use PPA\dbal\query\builder\AST\expressions\Expression;
use PPA\dbal\query\builder\AST\Operator;

class InLiterals extends Expression
{
    
    private $literals;

    public function __construct(array $literals)
    {
        $this->literals = $literals;
    }

    public function toString(): string
    {
        array_walk($this->literals, function(&$element) {
            $element = $element->toString();
        });
        
        return (new Operator(Operator::IN))->toString() . "(" . implode($this->literals, ", ") . ")";
    }

}

?>
