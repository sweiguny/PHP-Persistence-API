<?php

namespace PPA\tests\bootstrap\entity;

use PPA\orm\entity\Serializable;
use PPA\orm\mapping\annotations\Column;
use PPA\orm\mapping\annotations\Id;
use PPA\orm\mapping\annotations\Table;

/**
 * @Table(name="addr_state")
 */
class State implements Serializable
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
    private $country;

    public function __construct(string $name, int $country)
    {
        $this->name    = $name;
        $this->country = $country;
    }
    
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCountry(): int
    {
        return $this->country;
    }



}
?>
