<?php

namespace PPA;

/**
 * @copyright copyright (c) by Simon Weiguny <s.weiguny@gmail.com>
 * @author Simon Weiguny - 10.02.2014
 */
class Bootstrap {


    private function __construct() { }
    private function __clone() { }

    public static function boot() {
        require_once './Util.php';;
        spl_autoload_register(array('self', 'classload'));
    }

    private static function classload($class_name) {

        $path = null;
        $root = realpath(isset($path) ? $path : '.');

        if (($namespaces = Util::getNamespaces($class_name))) {
            $class_name = array_pop($namespaces);
            array_shift($namespaces);
            $directories = array();

            foreach ($namespaces as $directory) {
                $directories[] = $directory;
            }
            
            $root .= DIRECTORY_SEPARATOR . implode($directories, DIRECTORY_SEPARATOR);
        }

        $file = "$root/$class_name.php";

        if (file_exists($file)) {
            require_once $file;
        }
    }

    

}

function prettyDump($param) {
    echo "<pre>" . print_r($param, true) . "</pre>";
}

?>
