<?php

namespace PPA\dbal\statements;

use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\expressions\properties\Property;

class SQLStatement extends Property
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

    protected function getDriver(): DriverInterface
    {
        return $this->driver;
    }
    
}

?>
