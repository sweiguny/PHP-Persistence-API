<?php

namespace PPA\examples\entity;

use PPA\core\Entity;

class Order extends Entity {

    /**
     * @id
     * @column(name="id")
     */
    private $id;
    
    /**
     * @oneToMany(fetch = "lazy", mappedBy = "_PPA_examples_entity_OrderPosition")
     * @joinTable(x_column = "order_id")
     */
    protected $orderPos;
    
    
    
    
    public function __construct() {
        
    }

}

?>
