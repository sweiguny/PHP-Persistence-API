<?php

namespace PPA\core;

use PPA\core\exception\NoEntityException;
use ReflectionClass;

class EntityFactory
{

    /**
     * Instantiates an instance of the given classname, but without calling the
     * constructor. It is necessary, that the class is a subclass of \PPA\core\Entity.
     * 
     * @param string $classname
     * @return Entity An instance of the classname.
     * @throws NoEntityException
     */
    public static function create($classname)
    {
        $reflection = new ReflectionClass($classname);
        $$classname = $reflection->newInstanceWithoutConstructor();

        if ($$classname instanceof Entity)
        {
            return $$classname;
        }
        else
        {
            throw new NoEntityException($classname);
        }
    }

}

?>
