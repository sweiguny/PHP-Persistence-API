<?php

namespace PPA\dbal\statements\DQL\helper;

use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\SQLElementInterface;
use PPA\dbal\query\builder\CriteriaBuilder;

/**
 * Description of First
 *
 * @author siwe
 */
class Helper3 implements SQLElementInterface
{
    
    
    private $ASTCollection = [];
    
    /**
     *
     * @var DriverInterface
     */
    private $driver;
    
    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }
    
    public function orderBy()
    {

    }
    
    public function groupBy()
    {
        
    }
    
    public function toString(): string
    {
        
    }

}

?>
