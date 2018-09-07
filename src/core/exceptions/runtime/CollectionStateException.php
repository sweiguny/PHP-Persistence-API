<?php

namespace PPA\core\exceptions\runtime;

class CollectionStateException extends RuntimeException
{
    const CODE_EMPTY     = 1;
    const CODE_NOT_EMPTY = 2;
    
    const CODE_CRITERIA_CLEAN = 3;
    const CODE_CRITERIA_DIRTY = 4;
    
    const CODE_GROUP_CLEAN = 5;
    const CODE_GROUP_DIRTY = 6;
    
    const CODE_STATEMENT_CLEAN = 7;
    const CODE_STATEMENT_DIRTY = 8;
}

?>
