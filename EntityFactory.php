<?php

namespace PPA;

use ReflectionClass;

class EntityFactory {

    /**
     * Instantiates an instance of the given classname, but without calling the
     * constructor. It is necessary, that the class is a subclass of \PPA\Entity.
     * 
     * @param string $classname
     * @return object An instance of the classname.
     * @throws NoEntityException If the classname is not a subclass of \PPA\Entity.
     */
    public static function create($classname) {
        $reflection = new ReflectionClass($classname);
        $$classname = $reflection->newInstanceWithoutConstructor();
        
        if ($$classname instanceof Entity) {
            return $$classname;
        } else {
            throw new NoEntityException($classname);
        }
    }

}

?>
