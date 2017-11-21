<?php

namespace PPA\tests\bootstrap\entity;

use PPA\orm\entity\Serializable;
use PPA\orm\mapping\annotations\Column;
use PPA\orm\mapping\annotations\Id;
use PPA\orm\mapping\annotations\Table;

/**
 * @Table
 */
class Customer implements Serializable
{
    /**
     * @Id
     * @Column
     * 
     * @var int
     */
    private $customerNo;
    
    /**
     * @Column
     * 
     * @var string
     */
    private $firstname;
    
    /**
     * @Column
     * 
     * @var string
     */
    private $lastname;
    
    /**
     * @Column
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
