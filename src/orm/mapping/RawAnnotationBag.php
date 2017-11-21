<?php

namespace PPA\orm\mapping;

class RawAnnotationBag
{
    /**
     *
     * @var Annotatable
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
    
    public function __construct(Annotatable $owner, array $classAnnotations, array $propertyAnnotations)
    {
        $this->owner               = $owner;
        $this->classAnnotations    = $classAnnotations;
        $this->propertyAnnotations = $propertyAnnotations;
    }
    
    public function getOwner(): Annotatable
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
