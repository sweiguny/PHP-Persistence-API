<?php

namespace PPA;

/**
 * @copyright copyright (c) by Simon Weiguny <s.weiguny@gmail.com>
 * @author Simon Weiguny - 10.02.2014
 */
class EntityManager {

    private static $instance;

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new EntityManager();
        }
        return self::$instance;
    }
    
    private function __construct() {
        
    }
    
    public function persist(Entity $entity) {
        
    }

}

?>
