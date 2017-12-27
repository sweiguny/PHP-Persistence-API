<?php

namespace PPA\orm\entity;

use PPA\orm\EntityProperty;

/**
 * Description of ChangeSet
 *
 * @author siwe
 */
class Change
{
    /**
     *
     * @var EntityProperty
     */
    private $property;
    
    /**
     *
     * @var mixed
     */
    private $fromValue;
    
    /**
     *
     * @var mixed
     */
    private $toValue;
    
    public function __construct(EntityProperty $property, $fromValue, $toValue)
    {
        $this->property  = $property;
        $this->fromValue = $fromValue;
        $this->toValue   = $toValue;
    }
    
    public function getProperty(): EntityProperty
    {
        return $this->property;
    }

    public function getFromValue()
    {
        return $this->fromValue;
    }

    public function getToValue()
    {
        return $this->toValue;
    }
    
}

?>
