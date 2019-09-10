<?php

namespace PPA\orm\maps;

use PPA\orm\entity\Serializable;
use SplObjectStorage;

class OriginsMap
{
    use MapTrait;
    
    /**
     *
     * @var SplObjectStorage
     */
    private $map;
    
    public function __construct()
    {
        $this->map = new SplObjectStorage();
    }
    
    public function add(Serializable $entity)
    {
        // maybe we need https://github.com/myclabs/DeepCopy here!?!?!?!
        $this->map->attach(clone $entity);
    }
    
}

?>
