<?php

namespace PPA\tests\bootstrap\entity\dbint;

use PPA\orm\entity\Serializable;
use PPA\orm\mapping\annotations\Column;
use PPA\orm\mapping\annotations\Entity;
use PPA\orm\mapping\annotations\Id;

/**
 * @Entity(table="addr_street")
 */
class Street implements Serializable
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

    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

}
?>
