<?php

namespace PPA\sql;

/**
 * @copyright copyright (c) by Simon Weiguny <s.weiguny@gmail.com>
 * @author Simon Weiguny - 10.02.2014
 */
class Query {

    private $_query;
    
    public function __construct($query) {
        $this->_query = $query;
    }

    public function getResultList() {
        
    }
    
    public function getSingeResult() {
        
    }
}

?>
