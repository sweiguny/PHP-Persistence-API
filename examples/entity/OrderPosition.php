<?php

namespace PPA\examples\entity;

/**
 * @copyright copyright (c) by Simon Weiguny <s.weiguny@gmail.com>
 * @author Simon Weiguny - 10.02.2014
 * 
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
