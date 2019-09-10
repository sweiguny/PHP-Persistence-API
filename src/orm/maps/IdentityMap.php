<?php

namespace PPA\orm\maps;

use PPA\core\exceptions\ExceptionFactory;
use PPA\orm\entity\Serializable;
use PPA\orm\EntityAnalyser;
use SplObjectStorage;

class IdentityMap
{
    use MapTrait;
    
    /**
     *
     * @var array
     */
    private $map;
    
    /**
     *
     * @var EntityAnalyser
     */
    private $analyser;
    
    public function __construct(EntityAnalyser $analyser)
    {
        $this->analyser = $analyser;
        
        $this->map = [];
    }
    
    public function contains(Serializable $entity)
    {
        list($classname, $key) = $this->getImperatives($entity);
//        var_dump($this->map);
        if (isset($this->map[$classname]) && isset($this->map[$classname][$key]))
        {
            return true;
        }
        
        return false;
    }


    public function add(Serializable $entity): void
    {
        list($classname, $key) = $this->getImperatives($entity);
        
        if ($this->contains($entity))
        {
            throw ExceptionFactory::AlreadyExistentInIdentityMap($classname, $key);
        }
        else
        {
            $this->map[$classname][$key] = spl_object_hash($entity);
        }
    }
    
}

?>
