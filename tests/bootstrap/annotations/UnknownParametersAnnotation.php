<?php

namespace PPA\tests\bootstrap\annotations;

use PPA\orm\mapping\Annotation;

/**
 * @Target(value="CLASS")
 */
class UnknownParametersAnnotation implements Annotation
{
    /**
     * @Parameter
     * 
     * @var int
     */
    private $value;

}

?>
