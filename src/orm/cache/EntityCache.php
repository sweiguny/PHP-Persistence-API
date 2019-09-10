<?php

namespace \PPA\core\cache;

/**
 *
 * @author siwe
 */
class EntityCache
{
    /**
     *
     * @var Symfony\Component\Cache\Adapter\AdapterInterface
     */
    private $cache;


    public function __construct()
    {
        $itemsPool = new \Symfony\Component\Cache\Adapter\ApcuAdapter("items-pool.");
        $tagsPool = new \Symfony\Component\Cache\Adapter\ApcuAdapter("tags-pool.");
        $this->cache = new \Symfony\Component\Cache\Adapter\TagAwareAdapter($itemsPool, $tagsPool);
        
        $x = new Doctrine\ORM\EntityRepository($em, $class);
        
    }
}

?>
