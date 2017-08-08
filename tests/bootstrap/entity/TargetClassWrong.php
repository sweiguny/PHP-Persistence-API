<?php

namespace PPA\tests\bootstrap\entity;

use PPA\orm\entity\Serializable;

/**
 * @\PPA\orm\mapping\annotations\Table(name = "test")
 */
class TargetClassWrong implements Serializable
{
    /**
     * @\PPA\orm\mapping\annotations\Table(name = "test")
     * @\PPA\orm\mapping\annotations\Id
     * @\PPA\orm\mapping\annotations\Column
     *
     * @var int 
     */
    private $id;

}

?>
