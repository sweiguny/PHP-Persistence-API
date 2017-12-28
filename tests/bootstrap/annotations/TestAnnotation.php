<?php

namespace PPA\tests\bootstrap\annotations;

use PPA\orm\mapping\Annotation;

/**
 * @Target(value="CLASS")
 */
class TestAnnotation implements Annotation
{
    /**
     * @Parameter(datatype="integer")
     * 
     * @var int
     */
    private $value1;
    
    /**
     * @Parameter(datatype="integer")
     * 
     * @var int
     */
    private $value2;
    
    /**
     * @Parameter(required="true", datatype="integer")
     * 
     * @var int
     */
    private $value3;
    
    /**
     * @Parameter(datatype="integer")
     * 
     * @var int
     */
    private $value4 = 0;
    
    public function __construct(int $value1, int $value2)
    {
        $this->value1 = $value1;
        $this->value2 = $value2;
        $this->value3 = $value1;
    }

    public function setValue2(int $value2)
    {
        $this->value2 = $value2 * 2;
        $this->value4 = 1;
    }

    public function setValue3(int $value3)
    {
        $this->value3 = $value3 * 2;
    }
    
    public function getValue1()
    {
        return $this->value1;
    }

    public function getValue2()
    {
        return $this->value2;
    }

    public function getValue3()
    {
        return $this->value3;
    }

    public function getValue4()
    {
        return $this->value4;
    }

}

?>
