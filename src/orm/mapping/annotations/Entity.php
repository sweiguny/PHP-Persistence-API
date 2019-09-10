<?php

namespace PPA\orm\mapping\annotations;

use PPA\orm\mapping\Annotation;
use PPA\orm\repository\EntityRepository;

/**
 * @Target(value="CLASS")
 */
class Entity implements Annotation
{
    /**
     * @Parameter(default="%classname%", required="true", datatype="string")
     * 
     * @var string
     */
    private $table;
    
    /**
     * Hint: Don't use default-parameter here.
     * 
     * @Parameter(datatype="string")
     * 
     * @var string
     */
    private $repositoryclass = EntityRepository::class;
    
    public function __construct(string $table)
    {
        $this->table = $table;
    }
    
    public function getTable(): string
    {
        return $this->table;
    }

    public function getRepositoryClass(): string
    {
        return $this->repositoryclass;
    }
    
}

?>
