<?php

namespace PPA\orm\mapping;

use PPA\core\exceptions\ExceptionFactory;
use Symfony\Component\Finder\Finder;

class DataTypeMapper
{
    const DATATYPE_NAMESPACE = 'PPA\orm\mapping\types';
    
    /**
     *
     * @var array
     */
    private static $datatypeDirectories = [__DIR__ . DIRECTORY_SEPARATOR . "types"];

    /**
     *
     * @var array
     */
    private static $datatypeMap = [];

    public static function mapDatatype(string $datatype)
    {
        if (empty(self::$datatypeMap))
        {
            self::loadDatatypes();
        }
        
        if (!isset(self::$datatypeMap[$datatype]))
        {
            throw ExceptionFactory::ColumnTypeDoesNotExist($datatype);
        }
        
        return self::$datatypeMap[$datatype];
    }
    
    private static function loadDatatypes()
    {
        $finder = new Finder();
        $finder->files()->in(self::$datatypeDirectories)->notName("Abstract*");

        foreach ($finder as $file)
        {
            /* var $file SplFileInfo */
            
            require_once $file->getRealPath();
            
            $className   = explode(".", $file->getRelativePathname())[0];
            $typeName    = strtolower(substr($className, 4));
            $fqClassname = self::DATATYPE_NAMESPACE . "\\" . $className;
            
            self::$datatypeMap[$typeName] = new $fqClassname();
        }
    }
    
    public static function registerTypeDirectory(string $path)
    {
        if (!is_dir($path))
        {
            throw ExceptionFactory::NotADirectory($path);
        }
        
        if (in_array($path, self::$datatypeDirectories))
        {
            throw ExceptionFactory::TypeDirectoryAlreadyConsidered($path);
        }
        
        self::$datatypeDirectories[] = $path;
    }

}

?>
