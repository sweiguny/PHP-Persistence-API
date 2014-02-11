<?php

namespace PPA\examples\entity;

/**
 * @copyright copyright (c) by Simon Weiguny <s.weiguny@gmail.com>
 * @author Simon Weiguny - 10.02.2014
 * 
 * @table(name="user")
 */
class User extends \PPA\Entity {

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
     * @oneToOne(fetch="lazy", mappedby = "Role")
     */
    private $role;
    
    
    public function __construct() {
        
    }
    

}

?>
