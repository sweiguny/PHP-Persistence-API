<?php

namespace PPA\tests\bootstrap\entity\other;

use PPA\orm\entity\Serializable;
use PPA\orm\mapping\annotations\Column;
use PPA\orm\mapping\annotations\Id;
use PPA\orm\mapping\annotations\Table;

/**
 * @Table
 */
class ApcuEntity implements Serializable
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
    private $test;

    public function __construct(string $test)
    {
        $this->test = $test;
    }
    
    public function getTest()
    {
        return $this->test;
    }

    public function setTest($test)
    {
        $this->test = $test;
    }

}

?>
