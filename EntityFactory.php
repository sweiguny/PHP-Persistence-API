<?php

namespace PPA;

use InvalidArgumentException;
use ReflectionClass;

/**
 * @copyright copyright (c) by Simon Weiguny <s.weiguny@gmail.com>
 * @author Simon Weiguny - 10.02.2014
 */
class EntityFactory {

    public static function create($classname) {

        if (!EntityAnalyzer::isEntity($classname)) {
            throw new InvalidArgumentException("Class '{$classname}' must be a subclass of \\PPA\\Entity.");
        }

        $reflection = new ReflectionClass($classname);
        return $reflection->newInstanceWithoutConstructor();
    }

}

?>
