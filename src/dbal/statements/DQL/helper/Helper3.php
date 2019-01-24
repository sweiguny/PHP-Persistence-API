<?php

namespace PPA\dbal\statements\DQL\helper;

use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\ASTCollection;
use PPA\dbal\query\builder\AST\expressions\Having;
use PPA\dbal\query\builder\CriteriaBuilder;

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
    
//    private $collection = [];
    
    public function __construct(DriverInterface $driver)
    {
        parent::__construct();
        
        $this->driver = $driver;
    }
    
    public function having(): CriteriaBuilder
    {
        $criteriaBuilder = new CriteriaBuilder($this->driver);

        $this->collection[] = new Having();
        $this->collection[] = $criteriaBuilder;

        return $criteriaBuilder;
    }
    
    public function orderBy()
    {
        
    }

}

?>
