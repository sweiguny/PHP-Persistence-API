<?php

namespace PPA;

use PDO;
use PPA\core\EntityManager;
use PPA\core\exception\PPA_Exception;

class PPA {
    
    private static $instance;
    
    /**
     * @return PPA
     * @throws PPA_Exception
     */
    public static function getInstance() {
        if (self::$instance == null) {
            throw new PPA_Exception("PHP Persitence API must be initialized first.");
        }
        return self::$instance;
    }

    /**
     * @var PDO The database connection
     */
    private $conn;

    /**
     * This function in essence connects to the database using PDO, hence the
     * arguments are similar.
     * 
     * The connection will run in <b>autocommit-mode</b>. When you use transactions
     * the autocommit-mode will be turned off for the lifetime of the transaction.
     * When the transaction finished, the connection goes back to autocommit-mode.
     * 
     * Please see the PDO documentation for more information:
     * http://www.php.net/manual/de/conn.construct.php
     * 
     * @param string $dsn The <b>d</b>ata <b>s</b>ource <b>n</b>ame - see PDO-doc
     * @param string $username The database username - see PDO-doc
     * @param string $password The database password - see PDO-doc
     */
    public static function init($dsn, $username, $password) {
        if (self::$instance != null) {
            throw new PPA_Exception("PHP Persitence API already inited.");
        }
        
        require_once __DIR__ . '/Util.php';
        
        self::$instance = new self($dsn, $username, $password);
    }

    private function __clone() { }
    private function __construct($dsn, $username, $password) {
        register_shutdown_function(array($this, 'rollbackActiveTransaction'));
        set_exception_handler(array($this, 'handleException'));
        spl_autoload_register(array($this, 'classload'), true, true);
        
        $this->conn = new PDO($dsn, $username, $password, array(
            PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_AUTOCOMMIT => true
        ));
    }
    
    /**
     * Loads the php class respectively to the root folder.
     * 
     * @param string $class_name
     */
    public function classload($class_name) {
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
    
    public function handleException(\Exception $exception) {
        $this->rollbackActiveTransaction();
        throw $exception;
    }
    
    public function rollbackActiveTransaction() {
        if (EntityManager::getInstance()->inTransaction()) {
            EntityManager::getInstance()->rollback();
            # TODO: log: running transaction rolled back on shutdown.
        }
    }

    /**
     * @return PDO
     */
    public function getConnection() {
        return $this->conn;
    }

}

/**
 * This function is provided by PPA to show variables in a more human readable way.
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
