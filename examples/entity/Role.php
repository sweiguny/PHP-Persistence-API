<?php

namespace PPA\examples\entity;

/**
 * @copyright copyright (c) by Simon Weiguny <s.weiguny@gmail.com>
 * @author Simon Weiguny - 10.02.2014
 * 
 * @table = "role"
 */
class Role {

    /**
     * @id
     * @column(name="id")
     */
    private $id;
    
    /**
     * @column(name="name")
     */
    private $name;
    
    /**
     * @manyToMany(fetch = "lazy", mappedBy = "Right")
     * @joinTable(name = "role2right", column = "role_id", x_column = "right_id")
     */
    private $rights = array();
    
    
    public function __construct() {
        
    }

}

?>
