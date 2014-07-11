<?php

namespace PPA\core\exception;

use Exception;

class PPA_Exception extends Exception {

    /**
     * @param type $message
     * @param type $code
     */
    public function __construct($message, $code = null) {
        parent::__construct($message, $code);
    }

}

?>
