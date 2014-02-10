<?php

namespace PPA;

/**
 * @copyright copyright (c) by Simon Weiguny <s.weiguny@gmail.com>
 * @author Simon Weiguny - 10.02.2014
 */
class Util {
    
    public static function getNamespaces($class_name) {
        if (self::hasNamespace($class_name)) {
            return explode('\\', $class_name);
        }
        return null;
    }

    public static function hasNamespace($class_name) {
        if (strpos($class_name, '\\') !== false) {
            return true;
        }
        return false;
    }

}

?>
