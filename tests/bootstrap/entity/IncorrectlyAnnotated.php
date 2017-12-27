<?php

namespace PPA\tests\bootstrap\entity;

use PPA\orm\entity\Serializable;

/**
 * hudri wudri
 *     @PPA\orm\mapping\annotations\Table    (      name =  "hugo", style ='jorg hat keine umalute', class= "PPA\Test\gogo"  ,param1=val1,param2="val2"     )
 *     @xxx\Table    (      name =  "peter", style ='none', class= "no class"  ,param1=val1,param2="val2"     )
 * @Column
 * @\Column
 */
class IncorrectlyAnnotated implements Serializable
{
    /**
     * @\PPA\orm\mapping\annotations\Id
     * @Column(datatype="integer")
     *
     * @var int 
     */
    private $id;

    /**
     * @Column(datatype="integer")
     * 
     * @var int 
     */
    private $customer;

    public function __construct()
    {
        
    }

    
}

?>
