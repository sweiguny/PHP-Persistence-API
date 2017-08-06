<?php

namespace PPA\orm\mapping\annotations;

use PPA\orm\mapping\Annotation;

/**
 * @Target(value="CLASS")
 */
class Table implements Annotation
{
    /**
     * @Parameter(default='%classname%', required='true', type='string')
     * 
     * @var string
     */
    private $name;
    
}

?>
