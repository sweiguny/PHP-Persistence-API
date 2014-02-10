<?php

namespace PPA;

/**
 * @copyright copyright (c) by Simon Weiguny <s.weiguny@gmail.com>
 * @author Simon Weiguny - 10.02.2014
 */
class Entity {

    private $_id;
    private $_table;

    public function __construct() {
        echo get_class($this) . "<br>";
    }

}

?>
