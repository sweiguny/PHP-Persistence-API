<?php

namespace PPA\core\changeSet;

class EntityChange
{

    private $propertyName;
    private $from;
    private $to;

    public function __construct($propertyName, $from, $to)
    {
        $this->propertyName = $propertyName;
        $this->from         = $from;
        $this->to           = $to;
    }
    
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getTo()
    {
        return $this->to;
    }

}

?>