<?php

namespace PPA\dbal\event;

use PPA\core\PPA;
use PPA\dbal\drivers\DriverInterface;
use Symfony\Component\EventDispatcher\Event;

class ConnectionEvent extends Event
{
    const PRE_CONNECT  = PPA::EventPrefix . "pre-connect";
    const POST_CONNECT = PPA::EventPrefix . "post-connect";
    
    /**
     *
     * @var DriverInterface
     */
    private $driver;
    
    /**
     *
     * @var string
     */
    private $dataSourceName;

    /**
     *
     * @var string
     */
    private $username;
    
    public function __construct(DriverInterface $driver, string $dataSourceName, string $username)
    {
        $this->driver         = $driver;
        $this->dataSourceName = $dataSourceName;
        $this->username       = $username;
    }
    
    public function getDriver(): DriverInterface
    {
        return $this->driver;
    }

    public function getDataSourceName(): string
    {
        return $this->dataSourceName;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
    
}

?>
