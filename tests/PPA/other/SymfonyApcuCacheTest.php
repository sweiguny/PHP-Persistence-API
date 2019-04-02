<?php

namespace PPA\tests\other;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\Simple\ApcuCache;

/**
 * @group cache
 */
class SymfonyApcuCacheTest extends TestCase
{
    
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        
        
//        print_r(apcu_cache_info());
        apcu_clear_cache();
    }
    
    public function testApcuCacheWithoutPrefix()
    {
        $apcuCache1 = new ApcuCache();
        $apcuCache2 = new ApcuCache();
        
        $apcuCache1->set("test-key", "test-value");
        
        $this->assertTrue($apcuCache1->has("test-key"));
        $this->assertTrue($apcuCache2->has("test-key"));
    }
    
    public function testApcuCacheWithPrefix()
    {
        $apcuCache1 = new ApcuCache("cache1");
        $apcuCache2 = new ApcuCache("cache2");
        
        $apcuCache1->set("test-key", "test-value");
        
        $this->assertTrue($apcuCache1->has("test-key"));
        $this->assertFalse($apcuCache2->has("test-key"));
    }
    
    public function testApcuCacheWithTagging()
    {
        $itemsPool = new ApcuAdapter("items-pool");
        $tagsPool  = new ApcuAdapter("tags-pool");
        
        $cache = new TagAwareAdapter($itemsPool, $tagsPool);
        
        $item1 = $cache->getItem("tagged-key1");
        $item1->set("tagged-value1");
        $item1->tag(["tag-A"]);
        
        $item2 = $cache->getItem("tagged-key2");
        $item2->set("tagged-value2");
        $item2->tag(["tag-A", "tag-B"]);
        
        $item3 = $cache->getItem("tagged-key3");
        $item3->set("tagged-value3");
        $item3->tag(["tag-C", "tag-B"]);
        
        $cache->save($item1);
        $cache->save($item2);
        $cache->save($item3);
        
        $cache->invalidateTags(["tag-C"]);
        
        $item3 = $cache->getItem("tagged-key3");
        $this->assertFalse($item3->isHit());
    }
    
}

?>
