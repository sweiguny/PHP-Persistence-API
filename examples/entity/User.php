<?php

namespace PPA\examples\entity;

use PPA\core\Entity;

/**
 * @table(name="user")
 */
class User extends Entity {

    /**
     * @id
     * @column(name="id")
     */
    private $id;
    
    /**
     * @Column(name="username")
     */
    private $username;
    
    /**
     * @Column(name="role_id");
     * @oneToOne(fetch="lazy", mappedBy = "_PPA_examples_entity_Role")
     */
    private $role;
    
    public function getRole() {
        return $this->role;
    }

        
    public function __construct() {
        
    }
    
}

?>
