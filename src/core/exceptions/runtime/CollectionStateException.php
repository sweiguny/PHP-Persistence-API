<?php

namespace PPA\core\exceptions\runtime;

class CollectionStateException extends RuntimeException
{
    const CODE_CRITERIA_DIRTY = 2;
    const CODE_GROUP_DIRTY = 3;
    const CODE_STATEMENT_DIRTY = 4;
}

?>
