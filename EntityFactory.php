<?php

namespace PPA;

use InvalidArgumentException;
use ReflectionClass;

/**
 * @copyright copyright (c) by Simon Weiguny <s.weiguny@gmail.com>
 * @author Simon Weiguny - 10.02.2014
 */
class EntityFactory {

    /**
     * Instantiates an instance of the given classname, but without calling the
     * constructor. It is necessary, that the class is a subclass of \PPA\Entity.
     * 
     * @param string $classname
     * @return object An instance of the classname.
     * @throws InvalidArgumentException If the classname is not a subclass of \PPA\Entity.
     */
    public static function create($classname) {

        if (!EntityAnalyzer::isEntity($classname)) {
            throw new InvalidArgumentException("Class '{$classname}' must be a subclass of \\PPA\\Entity.");
        }

        $reflection = new ReflectionClass($classname);
        return $reflection->newInstanceWithoutConstructor();
    }

}

?>
