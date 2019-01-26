<?php

namespace PPA\dbal\statements\DML\helper;

use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\ASTCollection;
use PPA\dbal\statements\DQL\helper\WhereTrait;

/**
 * Description of BaseHelper
 *
 * @author siwe
 */
class BaseHelper extends ASTCollection
{
    use WhereTrait;//, GroupByTrait;
    
    /**
     *
     * @var DriverInterface
     */
    protected $driver;
    
//    private $collection = [];
    
    public function __construct(DriverInterface $driver)
    {
        parent::__construct();
        
        $this->driver = $driver;
    }
    
    
    
}

?>