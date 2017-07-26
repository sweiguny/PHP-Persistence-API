<?php

namespace PPA\dbal;

use PDOException;

class TransactionException extends PDOException
{

    public function __construct($message, $code = null)
    {
        parent::__construct($message, $code);
    }

}

?>
