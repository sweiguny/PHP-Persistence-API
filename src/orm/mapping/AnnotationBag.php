<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PPA\orm\mapping;

class AnnotationBag
{
    /**
     *
     * @var array
     */
    private $classAnnotations;
    
    /**
     *
     * @var array
     */
    private $propertyAnnotations;
    
    public function __construct(array $classAnnotations, array $propertyAnnotations)
    {
        $this->classAnnotations    = $classAnnotations;
        $this->propertyAnnotations = $propertyAnnotations;
    }

    public function getClassAnnotations()
    {
        return $this->classAnnotations;
    }

    public function getPropertyAnnotations()
    {
        return $this->propertyAnnotations;
    }


}

?>
