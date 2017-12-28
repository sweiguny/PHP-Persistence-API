<?php

namespace PPA\orm\mapping\annotations;

use PPA\orm\mapping\Annotation;

/**
 * @Target(value="CLASS")
 */
class Table implements Annotation
{
    /**
     * @Parameter(default="%classname%", required="true", datatype="string")
     * 
     * @var string
     */
    private $name;
    
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
}

?>
