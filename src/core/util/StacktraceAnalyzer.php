<?php

namespace PPA\core\util;

class StacktraceAnalyzer
{
    /**
     *
     * @var array
     */
    private $stacktrace;


    public function __construct(int $limit = 2, int $options = 0)
    {
        $this->stacktrace = debug_backtrace($options, $limit);
    }
    
    public function getCaller(): string
    {
        $stack = $this->stacktrace[1];
        $caller = [];
        $caller[] = $stack["function"] . "()";
        
        if (isset($stack["class"]))
        {
            $caller[] = "in";
            $caller[] = $stack["class"];
        }
        if (isset($stack["object"]))
        {
            $caller[] = "(" . get_class($stack["object"]) . ")";
        }
        
        return implode(" ", $caller);
    }

}

?>
