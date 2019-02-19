<?php

namespace PPA\dbal\drivers;

interface DriverInterface
{
    
    public function getDriverName(): string;
    
    public function getCharset(): string;
    
    public function getDefaultPort(): int;
    
    public function getDefaultOptions(): array;
    
    public function getSystemIdentifier(): string;
    
    public function getValueIdentifier(): string;
    
}

?>
