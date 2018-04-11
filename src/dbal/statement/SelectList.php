<?php

namespace PPA\dbal\statement;

class SelectList
{
    private $list = [];
    
    public function __construct(...$list)
    {
//        var_dump($list);
        $this->list = $list;
    }
    
    public function getList()
    {
        return $this->list;
    }

    public function addSourceToList($source)
    {
        $this->list[] = $source;
    }
    
}

?>
