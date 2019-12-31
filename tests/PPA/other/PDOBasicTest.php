<?php

namespace PPA\tests\other;

use ErrorException;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class PDOBasicTest extends TestCase
{
    
    public function testPDOInstantiation()
    {
        $this->expectException(PDOException::class);
        $this->expectExceptionMessage("invalid data source name");
        
        $pdo = new PDO("");
    }
    
    public function testPDOInstantiationByReflection()
    {
        // Temporarily set a custom error handler, to be able to catch the specific PDO message.
        set_error_handler(function(int $errno, string $errstr, string $errfile, int $errline) {
            throw new ErrorException($errstr, $errno, E_ERROR, $errfile, $errline);
        });
        
        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage("SQLSTATE[00000]: No error: PDO constructor was not called");

        /* @var $pdo PDO */
        $pdo = (new ReflectionClass(PDO::class))->newInstanceWithoutConstructor();
        $pdo->query("SELECT * FROM table"); // Triggers the error.

        // Reset the error handler to default.
        restore_error_handler();
    }
    
}

?>
