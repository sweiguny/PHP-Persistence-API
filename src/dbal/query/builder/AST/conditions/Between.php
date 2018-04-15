<?php

namespace PPA\dbal\query\builder\AST\conditions;

use PPA\dbal\query\builder\AST\expressions\Expression;
use PPA\dbal\query\builder\AST\expressions\NamedParameter;
use PPA\dbal\query\builder\AST\expressions\sources\Literal;
use PPA\dbal\query\builder\AST\expressions\UnnamedParameter;
use PPA\dbal\statement\SelectStatement;

class Between extends Expression
{
    
    private $parent;
    private $to;

    public function __construct(Criteria $parent)
    {
        $this->parent = $parent;
    }

    public function andLiteral($to): CriteriaCollection
    {
        $this->to = new Literal($to, gettype($to));
        
        return $this->parent->end();
    }

    public function andSubquery(SelectStatement $to): CriteriaCollection
    {
        $this->to = $to;
        
        return $this->parent->end();
    }

    public function andParameter(string $name = null): CriteriaCollection
    {
        $this->to = $name == null ? new UnnamedParameter() : new NamedParameter($name);;
        
        return $this->parent->end();
    }
    
    public function toString(): string
    {
        return $this->to->toString();
    }
    
}

?>
