<?php

namespace PPA\orm\event\transactions;

use PPA\core\PPA;

class TransactionCommitEvent extends TransactionEvent
{
    const NAME = PPA::TransactionEventPrefix . "commit";
}

?>
