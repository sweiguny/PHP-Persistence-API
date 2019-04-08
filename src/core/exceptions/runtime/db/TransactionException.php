<?php

namespace PPA\core\exceptions\runtime\db;

use PDOException;

class TransactionException extends PDOException
{

    public function __construct($message, $code = null)
    {
        parent::__construct($message, $code);
    }

}

?>
