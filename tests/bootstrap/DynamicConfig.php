<?php

namespace PPA\tests\bootstrap;

use DOMDocument;
use DOMNode;
use PPA\dbal\DriverManager;
use const PPA_TEST_CONFIG_PATH;

/**
 * Dynamically creates 
 */
class DynamicConfig
{
    const defaultPathForBaseConfig = PPA_TEST_CONFIG_PATH . DIRECTORY_SEPARATOR . "phpunit.xml";
    const defaultPathForExtConfig  = PPA_TEST_CONFIG_PATH . DIRECTORY_SEPARATOR . "phpunit.dist.xml";
    
    /**
     *
     * @var array
     */
    private $excludes = [];
    
    /**
     *
     * @var DOMDocument
     */
    private $dom;

    /**
     *
     * @var DOMNode
     */
    private $excludeNode;
    
    /**
     *
     * @var array
     */
    private $availableDrivers;

    public function __construct(string $path = null)
    {
        $this->dom = new DOMDocument();
        $this->dom->load($path ?: self::defaultPathForBaseConfig);
        
        $this->excludeNode      = $this->dom->createElement("exclude");
        $this->availableDrivers = DriverManager::getAvailableDrivers();
    }

    public function writeDynamicConfig(bool $noExclusions, string $path = null): void
    {
        if (!$noExclusions)
        {
            $this->gatherExclusions();
        }
        
        if (!empty($this->excludes))
        {
            $this->prepareNodes();
        }
        
        $finalDomAsString = $this->dom->saveXML();
        //echo $finalDomAsString;

        $finalDom = new DOMDocument();
        $finalDom->loadXML($finalDomAsString);
        $finalDom->save($path ?: self::defaultPathForExtConfig);
    }
    
    private function gatherExclusions()
    {
        $this->excludeMysqlIfNotAvailable();
        $this->excludePgsqlIfNotAvailable();
    }
    
    private function prepareNodes()
    {
        for ($i = 0; $i < count($this->excludes); $i++)
        {
            $groupNode = $this->dom->createElement("group", $this->excludes[$i]);

            $this->excludeNode->appendChild($groupNode);
        }

        $groupsNode = $this->dom->createElement("groups");
        $groupsNode->appendChild($this->excludeNode);

        $this->dom->getElementsByTagName("phpunit")->item(0)->appendChild($groupsNode);
    }
    
    public function excludeMysqlIfNotAvailable(): void
    {
        if (empty($this->availableDrivers[DriverManager::MYSQL]))
        {
            $this->excludes[] = DriverManager::MYSQL;
        }
    }
    
    public function excludePgsqlIfNotAvailable(): void
    {
        if (empty($this->availableDrivers[DriverManager::PGSQL]))
        {
            $this->excludes[] = DriverManager::PGSQL;
        }
    }
    
    public function excludeApcuIfNotAvailable(): void
    {
        
    }
    
//    public function excludeMemcachedIfNotAvailable(): void
//    {
//        
//    }
    
}

?>
