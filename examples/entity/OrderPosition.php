<?php

namespace PPA\examples\entity;

use PPA\core\Entity;

/**
 * @table(name = "orderpos")
 */
class OrderPosition extends Entity {

    /**
     * @id
     * @column(name="id")
     */
    private $id;
    
    /**
     * @column(name = "order_id")
     */
      private $orderId;
    
    public function __construct() {
        
    }

}

?>
