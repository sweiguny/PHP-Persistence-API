<?php

namespace PPA\dbal\query\builder\AST\expressions\predicates;

use PPA\dbal\query\builder\AST\expressions\Expression;
use PPA\dbal\query\builder\AST\operators\Comparison;
use PPA\dbal\query\builder\AST\operators\logical\Conjunction;

class _Between extends Predicate
{
    public function __construct(Expression $left, Expression $from, Expression $to)
    {
        parent::__construct();
        
        $this->collection[] = $left;
        $this->collection[] = new Comparison(Comparison::BETWEEN);
        $this->collection[] = $from;
        $this->collection[] = new Conjunction();
        $this->collection[] = $to;
    }
}

?>
