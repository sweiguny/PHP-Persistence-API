<?php

namespace PPA\tests\bootstrap\entity\em;

use PPA\orm\mapping\annotations\Column;
use PPA\orm\mapping\annotations\Entity;
use PPA\orm\mapping\annotations\Id;

/**
 * @Entity
 */
class Entity2 extends Entity1
{

    public static $static = null;

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
//    private $column1;
    
    /**
     * @Column(datatype="string")
     * 
     * @var string
     */
    protected $column2;
    
    /**
     * @Column(datatype="int")
     * 
     * @var string
     */
    public $column3;
    
    private $oneToOneRelation;

    public function __construct(string $column1, string $column2, int $column3, object $oneToOneRelation)
    {
        $this->column1 = $column1;
        $this->column2 = $column2;
        $this->column3 = $column3;
        
        $this->oneToOneRelation = $oneToOneRelation;
    }

    public function setColumn3($column3)
    {
        $this->column3 = $column3;
    }
    
    public function setOneToOneRelation(object $oneToOneRelation)
    {
        $this->oneToOneRelation = $oneToOneRelation;
    }

}

?>
