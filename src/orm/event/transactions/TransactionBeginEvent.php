<?php

namespace PPA\orm\event\transactions;

use PPA\core\PPA;

class TransactionBeginEvent extends TransactionEvent
{
    const NAME = PPA::TransactionEventPrefix . "begin";
}

?>
