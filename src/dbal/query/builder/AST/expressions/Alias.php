<?php

namespace PPA\dbal\query\builder\AST\expressions;

/**
 * Description of Alias
 *
 * @author siwe
 */
trait Alias
{
    /**
     *
     * @var string
     */
    private $alias;
    
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias)
    {
        $this->alias = $alias;
    }
    
    protected function stringifyAlias(): string
    {
        return $this->getAlias() == null ? "" : " AS " . $this->getAlias();
    }
    
}

?>
