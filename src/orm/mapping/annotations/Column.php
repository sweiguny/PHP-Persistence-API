<?php

namespace PPA\orm\mapping\annotations;

use PPA\orm\mapping\Annotation;
use PPA\orm\mapping\DataTypeMapper;
use PPA\orm\mapping\types\AbstractDatatype;

/**
 * @Target(value="PROPERTY")
 */
class Column implements Annotation
{
    /**
     * @Parameter(default="%propertyname%", required="true", datatype="string")
     * 
     * @var string
     */
    private $name;
    
    /**
     * @Parameter(required="true", datatype="string")
     * 
     * @var AbstractDatatype
     */
    private $datatype;

    public function __construct(string $name, string $datatype)
    {
        $this->name     = $name;
        $this->datatype = DataTypeMapper::mapDatatype($datatype);
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getDatatype(): AbstractDatatype
    {
        return $this->datatype;
    }

}

?>
