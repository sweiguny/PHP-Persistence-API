<?php

namespace PPA;

use PDO;

/**
 * @copyright copyright (c) by Simon Weiguny <s.weiguny@gmail.com>
 * @author Simon Weiguny - 10.02.2014
 */
class Bootstrap {

    private static $pdo;

    public static function boot($dsn, $username, $password) {
        require_once './Util.php';;
        
        spl_autoload_register(array('self', 'classload'));
        
        self::$pdo = new PDO($dsn, $username, $password);
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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

    public static function getPDO() {
        return self::$pdo;
    }


    private function __construct() { }
    private function __clone() { }

}

function prettyDump($param) {
    echo "<pre>" . print_r($param, true) . "</pre>";
}

?>
