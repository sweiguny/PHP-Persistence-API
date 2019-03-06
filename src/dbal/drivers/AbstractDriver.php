<?php

namespace PPA\dbal\drivers;

use PDO;

abstract class AbstractDriver implements DriverInterface
{
    /**
     * @const array
     */
    const DEFAULT_OPTIONS = [
            PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
//            PDO::ATTR_AUTOCOMMIT => true // not available for pgsql
        ];

    public function __construct(array $options = [])
    {
        $this->options = $this->getDefaultOptions() + self::DEFAULT_OPTIONS + $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
    
    public function __toString(): string
    {
        return "driver:" . $this->getDriverName();
    }

}

?>
