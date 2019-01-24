<?php

namespace PPA\dbal\statements\DQL\helper;

use PPA\dbal\query\builder\AST\expressions\Where;
use PPA\dbal\query\builder\CriteriaBuilder;

trait WhereTrait
{
    public function where(): CriteriaBuilder
    {
        $criteriaBuilder = new CriteriaBuilder($this->driver);

        $this->collection[] = new Where();
        $this->collection[] = $criteriaBuilder;

        return $criteriaBuilder;
    }
}

?>
