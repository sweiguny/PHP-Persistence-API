<?php

namespace PPA;

/**
 * @copyright copyright (c) by Simon Weiguny <s.weiguny@gmail.com>
 * @author Simon Weiguny - 10.02.2014
 */
class Entity {

    
    private $_id;
    private $_table;
    private $_classname;


    public function __construct() {
        $this->_classname = get_class($this);
        
        $this->extractMetaData();
        
        var_dump($this);
    }

    private function extractMetaData() {
        $this->extractTableName();
    }
    
    private function extractTableName() {
        
        
        $reflection = new \ReflectionClass($this);
        
        $doccomment = explode("*", substr($reflection->getDocComment(), 3, -2));
        
//        prettyDump($doccomment);
        
        foreach ($doccomment as $doc) {
            $pattern = "/@table/i";
            preg_match($pattern, $doc, $matches);
            
            if (!empty($matches)) {
                $doc = explode("=", $doc);
                
                preg_match("/[\"\'](.*)[\"\']/", $doc[1], $matches);
                
                if (empty($matches)) {
                    throw new \Exception("Syntax Error: Table");
                } else {
                    $this->_table = strtolower($matches[1]);
                    break;
                }
            }
        }
        if ($this->_table == null) {
            $namespaces = Util::getNamespaces($this->_classname);
            $this->_table = array_pop($namespaces);
        }
    }
    
}

?>
