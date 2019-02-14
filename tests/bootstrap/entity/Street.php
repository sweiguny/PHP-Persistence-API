<?php

namespace PPA\tests\bootstrap\entity;

use PPA\orm\entity\Serializable;
use PPA\orm\mapping\annotations\Column;
use PPA\orm\mapping\annotations\Id;
use PPA\orm\mapping\annotations\Table;

/**
 * @Table(name="addr_street")
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
