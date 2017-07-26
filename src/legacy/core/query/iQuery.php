<?php

namespace PPA\core\query;

interface iQuery extends iRetrieval
{

    public function getResultList();
    public function getSingleResult();

}

?>
