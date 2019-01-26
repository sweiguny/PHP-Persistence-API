<?php

namespace PPA\dbal\statements\DML\helper;

use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\expressions\FieldReference;
use PPA\dbal\query\builder\AST\expressions\NamedParameter;
use PPA\dbal\query\builder\AST\expressions\properties\Literal;
use PPA\dbal\query\builder\AST\expressions\UnnamedParameter;
use PPA\dbal\query\builder\AST\Operator;

class ToHelper extends BaseHelper
{
    /**
     *
     * @var SetClauseHelper 
     */
    private $parent;
    
    public function __construct(DriverInterface $driver, SetClauseHelper $parent)
    {
        parent::__construct($driver);
        
        $this->parent = $parent;
        
        $this->collection[] = new Operator(Operator::EQUALS);
    }
    
    public function toParameter(string $name = null): SetClauseHelper
    {
        $this->collection[] = $name == null ? new UnnamedParameter() : new NamedParameter($name);
        
        return $this->end();
    }
    
    public function toField(string $fieldName): SetClauseHelper
    {
        $this->collection[] = new FieldReference($fieldName);
        
        return $this->end();
    }
    
    public function toLiteral(string $literal): SetClauseHelper
    {
        $this->collection[] = new Literal($literal, gettype($literal));
        
        return $this->end();
    }

    private function end(): SetClauseHelper
    {
        $this->parent->getState()->setStateClean();
        
        return $this->parent;
    }
    
}

?>
