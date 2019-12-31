<?php

namespace PPA\dbal;

use PPA\dbal\drivers\AbstractDriver;

interface ConnectionInterface
{
    
    public function connect(): void;
    public function disconnect(): void;
    public function isConnected(): bool;
    public function getDataSourceName(): string;
    public function getDriver(): AbstractDriver;
//    public function getPDO(): \PDO;
    public function getHostname(): string;
    public function getDatabase(): string;
    public function getPort(): int;
    
    public function inTransaction(): bool;
    public function rollback(): void;
    public function commit(): void;
    public function begin(): void;
//    public function executeSql(string $query, array $parameters);
}

?>
