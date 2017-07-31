<?php

namespace PPA\core;

class HistoryEntity extends Entity
{

    public function __construct()
    {
        parent::__construct();
        
        EntityObserver::registerEntity($this);
    }

}

?>
