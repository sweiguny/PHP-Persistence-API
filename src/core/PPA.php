<?php

namespace PPA\core;

class PPA
{
    const ApplicationName             = "PHP-Persistence-API";
    const ApplicationShortName        = "PPA";
    const EventPrefix                 = self::ApplicationShortName . ".";
    const EntityManagementEventPrefix = self::EventPrefix . "entityManagement.";
    const TransactionEventPrefix      = self::EventPrefix . "transaction.";
}

?>