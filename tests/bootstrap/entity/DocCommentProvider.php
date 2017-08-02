<?php

namespace PPA\tests\bootstrap\entity;

use PPA\orm\entity\Serializable;

/**
 * This class provides the docComments for the tests.
 * All docComments are to attach to the class properties.
 * The values of the properties are the expected parsing results.
 */
class DocCommentProvider implements Serializable
{
    public $testEmpty1 = [];
    
    //
    public $testEmpty2 = [];
    
    /**/
    public $testEmpty3 = [];
    
    /* */
    public $testEmpty4 = [];
    
    /**
     * 
     
     * 
     */
    public $testEmpty5 = [];
    
    /**
     * @todo Nothing
     * @var array 
     */
    public $testEmpty6 = [];
    
    /**
     * @PPA\mapping\annotations\Id
     * @PPA\mapping\annotations\Column(name = "order")
     * @PPA\mapping\annotations\OneToMany(fetch = "lazy", mapped_by = "PPA\tests\bootstrap\entity\Order", cascade="all")
     * @PPA\mapping\annotations\JoinTable(x_column = "order_id")
     * 
     * @var array 
     */
    public $testPretty = [
        "PPA\mapping\annotations\Id"        => [],
        "PPA\mapping\annotations\Column"    => ["name"     => "order"],
        "PPA\mapping\annotations\OneToMany" => ["fetch"    => "lazy", "mapped_by" => 'PPA\tests\bootstrap\entity\Order', "cascade" => "all"],
        "PPA\mapping\annotations\JoinTable" => ["x_column" => "order_id"],
    ];
    
    /**
     *     @PPA\mapping\annotations\Id ( )     *@PPA\mapping\annotations\Column    (name = "order")
     *@PPA\mapping\annotations\OneToMany(  fetch='lazy'        ,   mapped_by = "PPA\tests\bootstrap\entity\Order"   , cascade=  "all"   )
     * @PPA\mapping\annotations\JoinTable( x_column =                 "order_id" )
     * 
     * @var array 
     */
    public $testUgly = [
        "PPA\mapping\annotations\Id"        => [],
        "PPA\mapping\annotations\Column"    => ["name"     => "order"],
        "PPA\mapping\annotations\OneToMany" => ["fetch"    => "lazy", "mapped_by" => 'PPA\tests\bootstrap\entity\Order', "cascade" => "all"],
        "PPA\mapping\annotations\JoinTable" => ["x_column" => "order_id"],
    ];
    
    /**
     * @PPA\mapping\annotations\Column(name = "test")
     * @PPA\mapping\annotations\Column(name = "hudri wudri")
     * @PPA\mapping\annotations\Column(NAME = "  order of everything ")
     * 
     * @var array
     */
    public $testParameters1 = [
        "PPA\mapping\annotations\Column" => ["name" => "  order of everything "]
    ];
    
    /**
     * @PPA\mapping\annotations\Column(name = "
     * test
     * 
     * ")
     * 
     * @var array
     */
    public $testParameters2 = [
        "PPA\mapping\annotations\Column" => []
    ];
    
}

?>
