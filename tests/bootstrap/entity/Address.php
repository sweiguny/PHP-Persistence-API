<?php

namespace PPA\tests\bootstrap\entity;

use PPA\orm\entity\Serializable;
use PPA\orm\mapping\annotations\Column;
use PPA\orm\mapping\annotations\Id;
use PPA\orm\mapping\annotations\Table;

/**
 * @Table(name="address")
 */
class Address implements Serializable
{
    /**
     * @Id
     * @Column(datatype="integer")
     * 
     * @var int
     */
    private $id;
    
    /**
     * @Column(datatype="integer")
     * 
     * @var int
     */
    private $country;
    
    /**
     * @Column(datatype="integer")
     * 
     * @var int
     */
    private $city;
    
    /**
     * @Column(datatype="integer")
     * 
     * @var int
     */
    private $street;

    /**
     * @Column(name="house_number")
     * 
     * @var string
     */
    private $housenumber;
    
    public function __construct(int $country, int $city, int $street, string $housenumber)
    {
        $this->country     = $country;
        $this->city        = $city;
        $this->street      = $street;
        $this->housenumber = $housenumber;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCountry(): int
    {
        return $this->country;
    }

    public function getCity(): int
    {
        return $this->city;
    }

    public function getStreet(): int
    {
        return $this->street;
    }

    public function getHousenumber(): string
    {
        return $this->housenumber;
    }
    
//    public function __toString(): string
//    {
//        return "{$this->city->getZipcode()} {$this->city->getName()}\n{$this->street->getName()} {$this->housenumber}\n{$this->country->getName()}";
//    }

}
?>
