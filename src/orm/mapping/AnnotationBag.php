<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PPA\orm\mapping;

use PPA\orm\entity\Serializable;

class AnnotationBag
{
    /**
     *
     * @var Serializable
     */
    private $owner;

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
    
    public function __construct(Serializable $owner, array $classAnnotations, array $propertyAnnotations)
    {
        $this->owner               = $owner;
        $this->classAnnotations    = $classAnnotations;
        $this->propertyAnnotations = $propertyAnnotations;
    }
    
    public function getOwner(): Serializable
    {
        return $this->owner;
    }

    public function getClassAnnotations(): array
    {
        return $this->classAnnotations;
    }

    public function getPropertyAnnotations(): array
    {
        return $this->propertyAnnotations;
    }


}

?>
