<?php

namespace PPA\core\changeSet;

class EntityChangeSet
{

    private $changes = [];

    public function __construct()
    {
    }
    
    public function getChanges()
    {
        return $this->changes;
    }

    public function addChange(EntityChange $change)
    {
        $this->changes[] = $change;
    }

}

?>