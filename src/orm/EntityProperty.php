<?php

namespace PPA\orm;

use PPA\orm\mapping\annotations\Column;
use PPA\orm\mapping\annotations\Relation;
use ReflectionProperty;

class EntityProperty extends ReflectionProperty
{
    /**
     *
     * @var Column
     */
    protected $column;
//    protected $relation;
    protected $isPrimary;

    public function __construct(string $class, string $name)
    {
        parent::__construct($class, $name);
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getColumn(): Column
    {
        return $this->column;
    }

    public function setColumn(Column $column): void
    {
        $this->column = $column;
    }

    /**
     * @param Relation $relation
     */
//    public function setRelation(Relation $relation)
//    {
//        $this->relation = $relation;
//    }

    /**
     * @return Relation
     */
//    public function getRelation()
//    {
//        return $this->relation;
//    }

    /**
     * Checks if the property represents a relation to another entity.
     * 
     * @return bool
     */
//    public function hasRelation()
//    {
//        return isset($this->relation);
//    }

    /**
     * Checks if the property is the primary.
     * 
     * @return bool
     */
    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    /**
     * Defines the property as primary.
     * 
     * @param bool $primary 
     */
    public function makePrimary($primary = true): void
    {
        $this->isPrimary = (bool) $primary;
    }

}

?>
