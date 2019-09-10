<?php

namespace PPA\tests\bootstrap\entity\analyser;

use PPA\orm\entity\Serializable;
use PPA\orm\mapping\annotations\Column;
use PPA\orm\mapping\annotations\Id;

class NoEntityAnnotation implements Serializable
{
    /**
     * @Id
     * @Column(datatype="integer")
     * 
     * @var int
     */
    private $customerNo;
    
    /**
     * @Column(datatype="string")
     * 
     * @var string
     */
    private $firstname;
    
    /**
     * @Column(datatype="string")
     * 
     * @var string
     */
    private $lastname;
    
    /**
     * @Column(datatype="string")
     * 
     * @var string
     */
    private $address;
    
    public function __construct($customerNo, $firstname, $lastname, $address)
    {
        $this->customerNo = $customerNo;
        $this->firstname  = $firstname;
        $this->lastname   = $lastname;
        $this->address    = $address;
    }
    
    public function getCustomerNo()
    {
        return $this->customerNo;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function getAddress()
    {
        return $this->address;
    }

}

?>
