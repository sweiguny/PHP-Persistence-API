<?php

namespace PPA\tests\bootstrap\entity\dbint;

use PPA\orm\entity\Serializable;
use PPA\orm\mapping\annotations\Column;
use PPA\orm\mapping\annotations\Entity;
use PPA\orm\mapping\annotations\Id;

/**
 * @Entity(table="addr_district")
 */
class District implements Serializable
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
    private $name;
    
    /**
     * @Column(datatype="integer")
     * 
     * @var int
     */
    private $state;

    public function __construct(string $name, int $state)
    {
        $this->name  = $name;
        $this->state = $state;
    }
    
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getState(): int
    {
        return $this->state;
    }

}
?>
