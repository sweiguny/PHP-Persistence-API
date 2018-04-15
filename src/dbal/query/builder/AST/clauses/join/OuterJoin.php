<?php

namespace PPA\dbal\query\builder\AST\clauses\join;

/**
 * Description of Join
 *
 * @author siwe
 */
class OuterJoin extends Join
{
    


//    public function __construct(string $name, string $alias = null)
//    {
//        parent::__construct($name, $alias);
//    }

    public function toString(): string
    {
        return " OUTER" . parent::toString();
    }

}

?>
