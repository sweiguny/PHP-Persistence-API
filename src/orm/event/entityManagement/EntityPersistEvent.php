<?php

namespace PPA\orm\event\entityManagement;

use PPA\core\PPA;

class EntityPersistEvent extends EntityManagementEvent
{
    const NAME = PPA::EntityManagementEventPrefix . "persist";
}

?>
