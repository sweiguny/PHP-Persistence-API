<?php

namespace PPA\orm\event\entityManagement;

use PPA\core\PPA;

class EntityRemoveEvent extends EntityManagementEvent
{
    const NAME = PPA::EntityManagementEventPrefix . "remove";
}

?>
