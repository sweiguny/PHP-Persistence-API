<?php

namespace PPA\dbal\query\builder;

class StatementStateCollection
{
    /**
     *
     * @var array
     */
    private $states;
    
    public function __construct()
    {
        
    }

    public function getStates(): array
    {
        return $this->states;
    }

    public function setStates(array $states)
    {
        $this->states = $states;
    }


}

?>
