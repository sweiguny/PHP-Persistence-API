<?php

namespace PPA\core\exception;

class TransactionException extends PPA_Exception
{

    public function __construct($message, $code = null)
    {
        parent::__construct($message, $code);
    }

}

?>
