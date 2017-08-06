<?php

namespace PPA\orm\mapping;

interface Annotation extends Annotatable
{
    const INDICATOR       = "@";
    const TARGET          = "Target";
    const TARGET_CLASS    = "CLASS";
    const TARGET_PROPERTY = "PROPERTY";
}

?>
