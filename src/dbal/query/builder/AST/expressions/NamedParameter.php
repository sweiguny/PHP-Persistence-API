<?php

namespace PPA\dbal\query\builder\AST\expressions;

class NamedParameter extends UnnamedParameter
{
    
    private $name;
    
    public function __construct($name)
    {
//        parent::__construct();
        
        $this->name = $name;
    }
    
    public function toString(): string
    {
        return ":{$this->name}";
    }

}

?>
