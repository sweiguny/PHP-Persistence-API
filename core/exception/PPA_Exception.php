<?php

namespace PPA\core\exception;

use Exception;

class PPA_Exception extends Exception {

    public function __construct($message, $code = null) {
        parent::__construct($message, $code);
    }

}

?>