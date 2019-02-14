<?php

namespace PPA\orm;

use IteratorAggregate;
use PPA\core\exceptions\ExceptionFactory;
use PPA\orm\mapping\AnnotationBag;
use Traversable;

class AnnotationMap implements IteratorAggregate
{
    /**
     *
     * @var array
     */
    private $annotations = [];

    public function __construct()
    {
        
    }
    
    public function exists(string $classname): bool
    {
        return isset($this->annotations[$classname]);
    }
    
    public function add(string $classname, $annotationBag): void
    {
        if ($this->exists($classname))
        {
            ExceptionFactory::AlreadyInDomain("Map already contains classname '{$classname}'. If you want to override the corresponding AnnotationBag, please use replace().");
        }
        
        $this->annotations[$classname] = $annotationBag;
    }
    
    public function replace(string $classname, $annotationBag): AnnotationBag
    {
        $oldAnnotationBag = $this->get($classname);
        $this->annotations[$classname] = $annotationBag;
        
        return $oldAnnotationBag;
    }
    
    public function get(string $classname): AnnotationBag
    {
        if (!$this->exists($classname))
        {
            ExceptionFactory::NotInDomain("Map doesn't contain classname '{$classname}'.");
        }
        
        return $this->annotations[$classname];
    }

    public function getIterator(): Traversable
    {
        return (function () {
                    foreach ($this->annotations as $classname => $annotationBag)
                    {
                        yield $classname => $annotationBag;
                    }
                    // Another example:
//                    while (list($key, $val) = each($this->items))
//                    {
//                        yield $key => $val;
//                    }
                })();
    }

}

?>
