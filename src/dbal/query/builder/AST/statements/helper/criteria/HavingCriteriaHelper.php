<?php

namespace PPA\dbal\query\builder\AST\statements\helper\criteria;

use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\statements\helper\BaseHelper;
use PPA\dbal\query\builder\AST\statements\helper\traits\CriteriaTrait;
use PPA\dbal\query\builder\AST\statements\helper\traits\OrderByTrait;

class HavingCriteriaHelper extends BaseHelper
{
    use CriteriaTrait;
    use OrderByTrait;
    
    /**
     *
     * @var BaseHelper
     */
    protected $parent;
    
    public function __construct(DriverInterface $driver, BaseHelper $parent = null)
    {
        parent::__construct($driver);
        
        $this->parent = $parent;
    }
}

?>
