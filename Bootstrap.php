<?php

namespace PPA;

use PDO;

class Bootstrap {

    /**
     * @var PDO The database connection
     */
    private static $pdo;

    /**
     * This function in essence connects to the database using PDO, hence the
     * arguments are similar.
     * 
     * Please see the PDO documentation for more information:
     * http://www.php.net/manual/de/pdo.construct.php
     * 
     * @param string $dsn The <b>d</b>ata <b>s</b>ource <b>n</b>ame - see PDO-doc
     * @param string $username The database username - see PDO-doc
     * @param string $password The database password - see PDO-doc
     */
    public static function boot($dsn, $username, $password) {
        require_once __DIR__ . '/Util.php';;
        
        spl_autoload_register(array('self', 'classload'), true, true);
        
        self::$pdo = new PDO($dsn, $username, $password);
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    /**
     * Loads the php class respectively to the root folder.
     * 
     * @param string $class_name
     */
    public static function classload($class_name) {
        $root = realpath(__DIR__);
        
        if (($namespaces = Util::getNamespaces($class_name))) {
            $class_name = array_pop($namespaces);
            array_shift($namespaces);
            
            $root .= DIRECTORY_SEPARATOR . implode($namespaces, DIRECTORY_SEPARATOR);
        }
        
        $file = $root . DIRECTORY_SEPARATOR . $class_name . ".php";

        if (file_exists($file)) {
            require_once $file;
        }
    }

    /**
     * @return PDO
     */
    public static function getPDO() {
        return self::$pdo;
    }


    private function __construct() { }
    private function __clone() { }

}

/**
 * This function is provided by PPA to show variables in a more human readable
 * way.
 * 
 * @param mixed $param
 */
function prettyDump($param) {
    if ($param == null) {
        echo "<pre>";
        var_dump($param);
        echo "</pre>";
    } else {
        echo "<pre>" . print_r($param, true) . "</pre>";
    }
}

?>
