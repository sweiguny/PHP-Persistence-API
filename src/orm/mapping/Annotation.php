<?php

namespace PPA\orm\mapping;

interface Annotation extends Annotatable
{
    const INDICATOR       = "@";
    const TARGET          = "Target";
    const TARGET_CLASS    = "CLASS";
    const TARGET_PROPERTY = "PROPERTY";
    
    
    const DATATYPE_STRING  = "string";
    const DATATYPE_INTEGER = "integer";
    
    const INTERNAL_DATATYPES = [
            self::DATATYPE_STRING,
            self::DATATYPE_INTEGER
        ];
}

?>
