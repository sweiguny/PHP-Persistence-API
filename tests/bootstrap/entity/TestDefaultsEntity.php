<?php

namespace PPA\tests\bootstrap\entity;

/**
 * @PPA\orm\mapping\annotations\Table
 */
class TestDefaultsEntity implements \PPA\orm\entity\Serializable
{
    /**
     * @\PPA\orm\mapping\annotations\Column
     * 
     * @var string Holds the expected name for the annotation
     */
    private $column = "column";
    
    public function getColumn()
    {
        return $this->column;
    }

}

?>
