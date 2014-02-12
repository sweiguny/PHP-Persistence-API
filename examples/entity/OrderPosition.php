<?php

namespace PPA\examples\entity;

/**
 * @table = "order_pos"
 */
class OrderPosition {

    /**
     * @id
     * @column(name="id")
     */
    private $id;
    
    /**
     * @oneToMany(fetch = "lazy", mappedBy = "OrderPosition")
     */
    private $orderId;
    
    public function __construct() {
        
    }

}

?>
