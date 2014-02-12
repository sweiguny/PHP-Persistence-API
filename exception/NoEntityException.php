<?php

namespace PPA\exception;

class NoEntityException extends PPA_Exception {
    
    public function __construct($classname) {
        parent::__construct("'{$classname}' is not an Entity.");
    }
}

?>
