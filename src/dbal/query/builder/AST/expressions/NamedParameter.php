<?php

namespace PPA\dbal\query\builder\AST\expressions;

class NamedParameter extends Expression
{
    /**
     *
     * @var string
     */
    private $name;
    
    public function __construct(string $name)
    {
        parent::__construct(false);
        
        $this->name = $name;
    }
    
    public function toString(): string
    {
        return ":{$this->name}";
    }
    
}

?>
