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
     * @Column(name="password")
     */
    private $password;
    
    /**
     * @Column(name="role_id");
     * @oneToOne(fetch="eager", mappedBy = "_PPA_examples_entity_Role", cascade="all")
     */
    private $role;
    
    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function getRole() {
        return $this->role;
    }
    
    public function setRole(Role $role) {
        $this->role = $role;
    }
    
}

?>
