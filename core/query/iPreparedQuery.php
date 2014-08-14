<?php

namespace PPA\core\query;

interface iPreparedQuery extends iRetrieval {

    public function getResultList(array $values);
    public function getSingleResult(array $values);
    
}

?>
