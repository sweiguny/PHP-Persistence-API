<?php

namespace PPA\tests\bootstrap\entity\analyser;

/**
 * @PPA\orm\mapping\annotations\Entity
 */
class TestDefaultsEntity implements \PPA\orm\entity\Serializable
{
    /**
     * @\PPA\orm\mapping\annotations\Column(datatype="string")
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
