<?php

namespace PPA\dbal\query\builder\AST\clauses\join;

/**
 * Description of Join
 *
 * @author siwe
 */
class LeftOuterJoin extends OuterJoin
{
    


//    public function __construct(string $name, string $alias = null)
//    {
//        parent::__construct($name, $alias);
//    }

    public function toString(): string
    {
        return " LEFT" . parent::toString();
    }

}

?>
