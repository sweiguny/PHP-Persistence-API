<?php

namespace PPA\dbal\statements\DQL\helper;

use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\ASTCollection;

/**
 * Description of First
 *
 * @author siwe
 */
class Helper3 extends ASTCollection
{
    
    
    /**
     *
     * @var DriverInterface
     */
    private $driver;
    
    public function __construct(DriverInterface $driver)
    {
        parent::__construct();
        
        $this->driver = $driver;
    }
    
    public function orderBy()
    {

    }
    
    public function groupBy()
    {
        
    }
    

}

?>
