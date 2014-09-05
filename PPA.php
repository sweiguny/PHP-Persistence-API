<?php

namespace PPA;

use Exception;
use PDO;
use PPA\core\EntityManager;
use PPA\core\exception\PPA_Exception;
use PPA\core\iPPA_Logger;
use PPA\core\PPA_DummyLogger;

class PPA {
    
    # Options for logging
    const OPTION_LOG_PREPARES       = "LOG_PREPARES";
    const OPTION_LOG_EXECUTES       = "LOG_EXECUTES";
    const OPTION_LOG_AFFECTIONS     = "LOG_AFFECTIONS";
    const OPTION_LOG_RETRIEVES      = "LOG_RETRIEVES";
    const OPTION_LOG_NOTIFICATIONS  = "LOG_NOTIFICATIONS";
    
    # Message classes for logging
    const MSG_CLASS_PREPARE         = "PREPARE";
    const MSG_CLASS_EXECUTE         = "EXECUTE";
    const MSG_CLASS_AFFECTION       = "AFFECTION";
    const MSG_CLASS_RETRIEVE        = "RETRIEVE";
    const MSG_CLASS_NOTIFICATION    = "NOTIFICATION";
    
    /**
     * @var PPA The Instance
     */
    private static $instance;
    
    /**
     * These are the standard logging options. They can be overwritten by
     * initialising PPA.
     * 
     * @var array The logging options.
     */
    private static $OPTIONS = array(
        self::OPTION_LOG_PREPARES               => true,
        self::OPTION_LOG_EXECUTES               => true,
        self::OPTION_LOG_AFFECTIONS             => true,
        self::OPTION_LOG_RETRIEVES              => true,
        self::OPTION_LOG_NOTIFICATIONS          => true
    );
    
    /**
     * @var array The existing message classes
     */
    private static $MSG_CLASSES = array(
        self::MSG_CLASS_PREPARE                 => array(3000, 5000),
        self::MSG_CLASS_EXECUTE                 => array(2000, 2500, 3001, 3501, 4000, 4500, 5001, 5501),
        self::MSG_CLASS_AFFECTION               => array(2020, 3020),
        self::MSG_CLASS_RETRIEVE                => array(2010, 2015, 2030, 2510, 3010, 3015, 3030, 3510, 4010, 4510, 5010, 5510),
        self::MSG_CLASS_NOTIFICATION            => array(1001, 1002, 1003, 1004, 1005, 1006, 1010, 1011, 1200)
    );
    
    /**
     * @var array The messages that can be logged
     */
    private static $messages = array(
        
        # Notifications
        1001 => "Lazy OneToOne-Relation - MockEntity will be created",
        1002 => "Eager OneToOne-Relation - Query will be created",
        1003 => "Lazy OneToMany-Relation - MockEntityList will be created",
        1004 => "Eager OneToMany-Relation - Query will be created",
        1005 => "Lazy ManyToMany-Relation - MockEntityList will be created",
        1006 => "Eager ManyToMany-Relation - Query will be created",
        1010 => "MockEntity exchanges itself with a real Entity ('\\%s')",
        1011 => "MockEntityList exchanges itself with a list of real Entities ('%s')",
        1200 => "Current logger ('\\%s') will be changed to '\\%s'",
        
        # Query
        2000 => "Executing query for single result: %s",
        2010 => "Retrieved one row",
        2015 => "Retrieved scalar: %s",
        2020 => "%u rows affected",
        2030 => "Last inserted primary key: %s",
        2500 => "Executing query for resultlist: %s",
        2510 => "Retrieved %u rows",
        
        # PreparedQuery
        3000 => "Preparing query: %s",
        3001 => "Executing query for single result with values: %s",
        3010 => "Retrieved one row",
        3015 => "Retrieved scalar: %s",
        3020 => "%u rows affected",
        3030 => "Last inserted primary key: %s",
        3501 => "Executing query for resultlist with values: %s",
        3510 => "Retrieved %s rows",
        
        # TypedQuery
        4000 => "Executing query for single result for class '%s': %s",
        4010 => "Retrieved one Entity ('\\%s') %s",
        4500 => "Executing query for resultlist for class '%s': %s",
        4510 => "Retrieved %u Entities",
        
        # PreparedTypedQuery
        5000 => "Preparing query for class '%s': %s",
        5001 => "Executing query for single result with values: %s",
        5010 => "Retrieved one Entity ('\\%s') %s",
        5501 => "Executing query for resultlist with values: %s",
        5510 => "Retrieved %u Entities"
    );

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
        self::$OPTIONS  = array_merge(self::$OPTIONS, $options);
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
     * @param array $params
     */
    public static function log($logCode, array $params = array()) {
        $message = null;
        
        if (in_array($logCode, self::$MSG_CLASSES[self::MSG_CLASS_PREPARE])) {
            if (self::$OPTIONS[self::OPTION_LOG_PREPARES]) {
                switch ($logCode) {
                    case 3000:
                        $message = sprintf(self::$messages[$logCode], $params[0]);
                        break;
                    case 5000:
                        $message = sprintf(self::$messages[$logCode], $params[0], $params[1]);
                        break;
                    default:
                        throw new PPA_Exception("LogCode {$logCode} is not properly handled.", 0);
                        break;
                }
            }
        } else if (in_array($logCode, self::$MSG_CLASSES[self::MSG_CLASS_EXECUTE])) {
            if (self::$OPTIONS[self::OPTION_LOG_EXECUTES]) {
                switch ($logCode) {
                    case 2000:
                    case 2500:
                    case 3001:
                    case 3501:
                    case 5001:
                    case 5501:
                        $message = sprintf(self::$messages[$logCode], $params[0]);
                        break;
                    case 4000:
                    case 4500:
                        $message = sprintf(self::$messages[$logCode], $params[0], $params[1]);
                        break;
                    default:
                        throw new PPA_Exception("LogCode {$logCode} is not properly handled.", 0);
                        break;
                }
            }
        } else if (in_array($logCode, self::$MSG_CLASSES[self::MSG_CLASS_AFFECTION])) {
            if (self::$OPTIONS[self::OPTION_LOG_AFFECTIONS]) {
                switch ($logCode) {
                    case 2020:
                    case 3020:
                        $message = sprintf(self::$messages[$logCode], $params[0]);
                        break;
                    default:
                        throw new PPA_Exception("LogCode {$logCode} is not properly handled.", 0);
                        break;
                }
            }
        } else if (in_array($logCode, self::$MSG_CLASSES[self::MSG_CLASS_RETRIEVE])) {
            if (self::$OPTIONS[self::OPTION_LOG_RETRIEVES]) {
                switch ($logCode) {
                    case 2010:
                    case 3010:
                        $message = self::$messages[$logCode];
                        break;
                    case 2015:
                    case 2020:
                    case 2030:
                    case 2510:
                    case 3015:
                    case 3020:
                    case 3030:
                    case 3510:
                    case 4510:
                    case 5510:
                        $message = sprintf(self::$messages[$logCode], $params[0]);
                        break;
                    case 4010:
                    case 5010:
                        $message = sprintf(self::$messages[$logCode], $params[0], $params[1]);
                        break;
                    default:
                        throw new PPA_Exception("LogCode {$logCode} is not properly handled.", 0);
                        break;
                }
            }
        } else if (in_array($logCode, self::$MSG_CLASSES[self::MSG_CLASS_NOTIFICATION])) {
            if (self::$OPTIONS[self::OPTION_LOG_NOTIFICATIONS]) {
                switch ($logCode) {
                    case 1001:
                    case 1002:
                    case 1003:
                    case 1004:
                    case 1005:
                    case 1006:
                        $message = self::$messages[$logCode];
                        break;
                    case 1010:
                    case 1011:
                        $message = sprintf(self::$messages[$logCode], $params[0]);
                        break;
                    case 1200:
                        $message = sprintf(self::$messages[$logCode], $params[0], $params[1]);
                        break;
                    default:
                        throw new PPA_Exception("LogCode {$logCode} is not properly handled.", 0);
                        break;
                }
            }
        } else {
            throw new PPA_Exception("LogCode {$logCode} cannot be recognized.", 0);
        }
        
        if ($message != null) {
            self::getInstance()->getLogger()->log($logCode, $message);
        }
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
        self::log(1200, array(get_class(self::getInstance()->getLogger()), get_class($logger)));
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
