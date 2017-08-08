<?php

namespace PPA\orm\mapping\annotations;

use PPA\orm\mapping\Annotation;

/**
 * @Target(value="PROPERTY")
 */
class Column implements Annotation
{
    /**
     * @Parameter(default='%propertyname%', required='true', type='string')
     * 
     * @var string
     */
    private $name;
    
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
}

?>
