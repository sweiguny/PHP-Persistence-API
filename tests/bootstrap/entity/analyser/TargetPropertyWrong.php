<?php

namespace PPA\tests\bootstrap\entity\analyser;

use PPA\orm\entity\Serializable;

/**
 * @\PPA\orm\mapping\annotations\Column
 */
class TargetPropertyWrong implements Serializable
{
    /**
     * @\PPA\orm\mapping\annotations\Id
     * @\PPA\orm\mapping\annotations\Column(datatype="integer")
     *
     * @var int 
     */
    private $id;

}

?>
