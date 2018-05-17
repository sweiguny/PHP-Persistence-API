<?php

namespace PPA\dbal\query\builder\AST\conditions;

use PPA\dbal\query\builder\AST\expressions\Expression;
use PPA\dbal\query\builder\AST\expressions\NamedParameter;
use PPA\dbal\query\builder\AST\expressions\properties\Literal;
use PPA\dbal\query\builder\AST\expressions\UnnamedParameter;
use PPA\dbal\query\builder\CriteriaBuilder;
use PPA\dbal\statement\SelectStatement;

class Between extends Expression
{
    /**
     *
     * @var Criteria
     */
    private $parent;

    public function __construct(Criteria $parent)
    {
        parent::__construct();
        
        $this->parent = $parent;
    }

    public function andLiteral($to): CriteriaBuilder
    {
        $this->collection[] = new Literal($to, gettype($to));
        
        return $this->end();
    }

    public function andSubquery(SelectStatement $to): CriteriaBuilder
    {
        $this->collection[] = $to;
        
        return $this->end();
    }

    public function andParameter(string $name = null): CriteriaBuilder
    {
        $this->collection[] = $name == null ? new UnnamedParameter() : new NamedParameter($name);;
        
        return $this->end();
    }
    
    private function end(): CriteriaBuilder
    {
        return $this->parent->end();
    }
    
}

?>
