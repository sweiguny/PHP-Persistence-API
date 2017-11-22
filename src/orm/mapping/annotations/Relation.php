<?php

namespace PPA\orm\mapping\annotations;

use PPA\core\exceptions\ExceptionFactory;
use PPA\orm\entity\Serializable;
use PPA\orm\mapping\Annotation;
use ReflectionClass;

abstract class Relation implements Annotation
{
    const FETCH_TYPE_EAGER = "eager";
    const FETCH_TYPE_LAZY  = "lazy";
    
    const CASCADE_TYPE_ALL     = "all";
    const CASCADE_TYPE_NONE    = "none";
    const CASCADE_TYPE_REMOVE  = "remove";
    const CASCADE_TYPE_PERSIST = "persist";
    
    private static $fetchTypes = [
        self::FETCH_TYPE_EAGER,
        self::FETCH_TYPE_LAZY
    ];

    private static $cascadeTypes = [
        self::CASCADE_TYPE_ALL,
        self::CASCADE_TYPE_NONE,
        self::CASCADE_TYPE_REMOVE,
        self::CASCADE_TYPE_PERSIST
    ];

    /**
     * The fully qualified classname of the related entity. Is case sensitive.
     * 
     * @Parameter(required="true", type="string")
     * 
     * @var string
     */
    private $mapped_by;
    
    /**
     * The fetch type. Can be "eager" or "lazy".
     * 
     * @Parameter(required="true", type="string")
     * 
     * @var string
     */
    private $fetch;
    
    /**
     * The cascade type. Can be "all", "none", "remove" or "persist".
     * 
     * @Parameter(required="true", type="string")
     * 
     * @var string
     */
    private $cascade;
    
    public function setMapped_by(string $mapped_by)
    {
        $class = new ReflectionClass($mapped_by);
        
        if (!$class->implementsInterface(Serializable::class))
        {
            throw ExceptionFactory::NotSerializable($mapped_by);
        }
        
        $this->mapped_by = $mapped_by;
    }

    public function setFetch(string $fetch)
    {
        if (!in_array($fetch, self::$fetchTypes))
        {
            throw ExceptionFactory::UnknownFetchType($fetch, self::$fetchTypes);
        }
        
        $this->fetch = $fetch;
    }

    public function setCascade(string $cascade)
    {
        if (!in_array($cascade, self::$cascadeTypes))
        {
            throw ExceptionFactory::UnknownCascadeType($cascade, self::$cascadeTypes);
        }
        
        $this->cascade = $cascade;
    }

}

?>
