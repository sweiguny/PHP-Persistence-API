<?php

namespace PPA\core\util;

use Generator;
use NoRewindIterator;

class FileReader
{
    
    public function getLineIterator(string $filepath): NoRewindIterator
    {
        return new NoRewindIterator($this->getLineGenerator($filepath));
    }
    
    private function getLineGenerator(string $filepath): Generator
    {
        $handle = fopen($filepath, "r");
        
        while (!feof($handle))
        {
            yield trim(fgets($handle));
        }

        fclose($handle);
    }
}

?>
