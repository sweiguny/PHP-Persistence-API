<?php

namespace PPA\dbal\drivers;

use PDO;

abstract class AbstractDriver implements DriverInterface
{
    /**
     *
     * @var array
     */
    private $options = [
            PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_AUTOCOMMIT => true
        ];

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->getDefaultOptions(), $this->options, $options);
    }

    public function getOptions(): array
    {
        return $this->options;
    }

}

?>
