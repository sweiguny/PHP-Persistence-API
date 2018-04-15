<?php

namespace PPA\dbal\query\builder\AST\expressions\sources;

/**
 * Description of Table
 *
 * @author siwe
 */
class Table extends Source
{
    /**
     *
     * @var string
     */
    private $name;
    
    public function __construct(string $name, string $alias = null)
    {
        parent::__construct($alias);
        
        $this->name = $name;
    }
    
    public function getName(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        return "`{$this->name}`" . $this->stringifyAlias();
    }

}

?>
