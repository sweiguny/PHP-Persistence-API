<?php

namespace PPA\examples\entity;

/**
 * @copyright copyright (c) by Simon Weiguny <s.weiguny@gmail.com>
 * @author Simon Weiguny - 10.02.2014
 * 
 * @table = 'right'
 */
class Right extends \PPA\Entity {
    
    /**
     * @id
     * @column(name="id")
     */
    private $id;
    
    /**
     * @column(name="name")
     */
    private $name;
    
    public function __construct() {
        parent::__construct();
//        echo get_class($this) . "<br>";
    }

}

?>
