<?php

namespace PPA\tests\bootstrap\entity;

use PPA\orm\entity\Serializable;

/**
 * @\PPA\orm\mapping\annotations\Column
 */
class TargetPropertyWrong implements Serializable
{
    /**
     * @\PPA\orm\mapping\annotations\Id
     * @\PPA\orm\mapping\annotations\Column
     *
     * @var int 
     */
    private $id;

}

?>
