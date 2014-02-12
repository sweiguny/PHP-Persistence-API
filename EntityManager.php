<?php

namespace PPA;

class EntityManager {

    private static $instance;

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new EntityManager();
        }
        return self::$instance;
    }
    
    /**
     * @var PDO the connection
     */
    private $_pdo;

    private function __construct() {
        $this->_pdo = Bootstrap::getPDO();
    }
    


    public function persist(Entity $entity) {
        
    }
    
    public function remove(Entity $entity) {
        
    }

}

?>
