<?php

namespace PPA\tests\bootstrap\entity;

use PPA\orm\entity\Serializable;

/**
 * @\PPA\orm\mapping\annotations\Table(name = "hugo")
 */
class WellAnnotated implements Serializable
{
    /**
     * @\PPA\orm\mapping\annotations\Table
     * @\PPA\orm\mapping\annotations\Id
     * @\PPA\orm\mapping\annotations\Column
     *
     * @var int 
     */
    private $id;

    /**
     * @\PPA\orm\mapping\annotations\Column
     * 
     * @var int 
     */
    private $customer;

    public function __construct()
    {
        
    }

}

?>
