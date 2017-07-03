<?php

namespace PPA;

class Util
{

    public static function getNamespaces($class_name)
    {
        if (self::hasNamespace($class_name))
        {
            return explode('\\', $class_name);
        }
        
        return null;
    }

    public static function hasNamespace($class_name)
    {
        if (strpos($class_name, '\\') !== false)
        {
            return true;
        }
        
        return false;
    }

}

?>
