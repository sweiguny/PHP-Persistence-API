<?php

namespace PPA\dbal\query\builder\AST\statements\helper;

use PPA\dbal\query\builder\AST\statements\helper\traits\SetTrait;
use PPA\dbal\query\builder\AST\statements\helper\traits\WhereTrait;

class SetClauseHelper extends BaseHelper
{
    use WhereTrait, SetTrait;
}

?>
