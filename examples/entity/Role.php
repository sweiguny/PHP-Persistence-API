<?php

namespace PPA\examples\entity;

use PPA\Entity;

class Role extends Entity {

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
    
    public function getName() {
        return $this->name;
    }

        
    public function __construct() {
        
    }

}

?>
