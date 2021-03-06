<?php

namespace PPA\tests\bootstrap\entity\dbint;

use PPA\orm\entity\Serializable;
use PPA\orm\mapping\annotations\Column;
use PPA\orm\mapping\annotations\Entity;
use PPA\orm\mapping\annotations\Id;

/**
 * @Entity(table="addr_city")
 */
class City implements Serializable
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
     * @Column(name="zip_code", datatype="string")
     * 
     * @var string
     */
    private $zipcode;
    
    /**
     * @Column(datatype="integer")
     * 
     * @var int
     */
    private $district;

    public function __construct(string $name, string $zipcode, int $district)
    {
        $this->name     = $name;
        $this->zipcode  = $zipcode;
        $this->district = $district;
    }
    
    public function getId(): int
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    public function getDistrict(): int
    {
        return $this->district;
    }

}
?>
