<?php

namespace PPA\tests\bootstrap\entity;

use PPA\orm\entity\Serializable;
use PPA\orm\mapping\annotations\Column;
use PPA\orm\mapping\annotations\Id;
use PPA\orm\mapping\annotations\Table;

/**
 * @Table(name="addr_country")
 */
class Country implements Serializable
{
    /**
     * @Id
     * @Column(datatype="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @Column(datatype="string")
     * 
     * @var string
     */
    private $name;

    /**
     * @Column(name="short_name", datatype="string")
     * 
     * @var string
     */
    private $shortname;

    public function __construct(string $name, string $shortname)
    {
        $this->name      = $name;
        $this->shortname = $shortname;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortname(): string
    {
        return $this->shortname;
    }

}

?>
