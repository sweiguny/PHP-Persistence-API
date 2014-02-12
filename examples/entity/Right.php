<?php

namespace PPA\examples\entity;

/**
 * @table = 'right'
 */
class Right extends \PPA\Entity {
    
    /**
     * @id = generatedValue
     * @column(name="id")
     */
    private $id;
    
    /**
     * @column(name="name")
     */
    private $desc;
    
    public function __construct() {
        parent::__construct();
//        echo get_class($this) . "<br>";
    }
    
    public function deny() {
        echo "perm denied!";
    }

}

?>
