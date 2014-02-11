<?php

namespace PPA\examples\entity;

use PPA\Entity;

/**
 * @copyright copyright (c) by Simon Weiguny <s.weiguny@gmail.com>
 * @author Simon Weiguny - 10.02.2014
 * 
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
     * @oneToOne(fetch="eager", mappedBy = "_PPA_examples_entity_Role")
     */
    private $role;
    
    
    public function __construct() {
        
    }
    

}

?>
