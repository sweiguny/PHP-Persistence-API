<?php

namespace PPA;

use Exception;
use PDO;
use PPA\core\EntityManager;
use PPA\core\exception\PPA_Exception;
use PPA\core\iPPA_Logger;
use PPA\core\PPA_DummyLogger;

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

    private static $OPTIONS = array(
        "LOG_INSERTS"               => true,
        "LOG_UPDATES"               => true,
        "LOG_DELETES"               => true,
        "LOG_NOTIFICATIONS"         => true,
    );

    /**
     * @var PDO The database connection
     */
    private $conn;
    
    /**
     *
     * @var iPPA_Logger
     */
    private $logger;

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
     * @param array $options Options for PPA. Mainly logging options.
     */
    public static function init($dsn, $username, $password, array $options = array()) {
        if (self::$instance != null) {
            throw new PPA_Exception("PHP Persitence API already inited.");
        }
        
        require_once __DIR__ . '/Util.php';
        
        self::$instance = new self($dsn, $username, $password);
        
        self::$OPTIONS = array_merge(self::$OPTIONS, $options);
    }
    
    /**
     * Sends a certain message to the assigned logger. This message will mainly
     * be used by PPA internally. Because the logging options need to be
     * checked, all messages that come from outside may be "absorbed".
     * 
     * You can assign a logger by means of PPA::init() or 
     * via PPA::getInstance()->setLogger();
     * Every custom logger must implement the iPPA_Logger interface.
     * If no custom logger was explicitly assigned, all messages will fall into
     * the nirvana.
     * 
     * @param int $logCode
     * @param string $message
     */
    public static function log($logCode, $message) {
        self::getInstance()->getLogger()->log($logCode, $message);
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
        
        $this->logger = new PPA_DummyLogger();
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
    
    public function handleException(Exception $exception) {
        $this->logger->log(1300, get_class($exception) . " occured with message: " . $exception->getMessage());
        $this->rollbackActiveTransaction();
        throw $exception;
    }
    
    public function rollbackActiveTransaction() {
        if (EntityManager::getInstance()->inTransaction()) {
            EntityManager::getInstance()->rollback();
            
            $this->logger->log(1100, "Transaction was rolled back, during shutdown.");
        }
    }

    /**
     * @return PDO
     */
    public function getConnection() {
        return $this->conn;
    }

    /**
     * Set a logger to enable logging of queries and specific PPA actions.
     * If there was already a logger set through the PPA::init() function, then
     * the current logger will be overwritten.
     * 
     * @param iPPA_Logger $logger
     */
    public function setLogger(iPPA_Logger $logger) {
        self::log(1200, "Current logger ('\\" . get_class(self::getInstance()->getLogger()) . "') will be changed to '\\" . get_class($logger) . "'");
        $this->logger = $logger;
    }
    
    /**
     * @return iPPA_Logger
     */
    public function getLogger() {
        return $this->logger;
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
