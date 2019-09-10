<?php

namespace PPA\tests\bootstrap\entity\em;

use PPA\orm\entity\Serializable;
use PPA\orm\mapping\annotations\Column;
use PPA\orm\mapping\annotations\Entity;
use PPA\orm\mapping\annotations\Id;

/**
 * @Entity
 */
class Entity1 implements Serializable
{
    /**
     * @Id
     * @Column(datatype="integer")
     * 
     * @var int
     */
    private $id;
    
    /**
     * @Column(datatype="string")
     * 
     * @var string
     */
    protected $column1;
    
    public function __construct($id)
    {
        $this->id = $id;
    }

}

?>
