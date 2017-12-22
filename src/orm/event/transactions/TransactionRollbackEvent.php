<?php

namespace PPA\orm\event\transactions;

use PPA\core\PPA;

class TransactionRollbackEvent extends TransactionEvent
{
    const NAME = PPA::TransactionEventPrefix . "rollback";
}

?>
