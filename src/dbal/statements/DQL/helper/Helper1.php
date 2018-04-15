<?php

namespace PPA\dbal\statements\DQL\helper;

/**
 * Description of First
 *
 * @author siwe
 */
class Helper1 extends BaseHelper
{
    
    public function join(string $joinTable, string $alias = null): Helper2
    {
        return parent::join($joinTable, $alias);
    }

}

?>
