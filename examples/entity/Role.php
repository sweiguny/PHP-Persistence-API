<?php

namespace PPA\examples\entity;

use PPA\core\Entity;

/*
 * @table(name='role')
 */
class Role extends \PPA\core\HistoryEntity
{
    /**
     * @id
     * @column(name="id")
     */
    private $id;

    /**
     * @track
     * @column(name="name")
     */
    private $name;

    /**
     * @manyToMany(fetch = "lazy", mappedBy = "_PPA_examples_entity_Right", cascade="none")
     * @joinTable(name = "role2right", column = "role_id", x_column = "right_id")
     */
    private $rights = [];

    public function __construct($name)
    {
        parent::__construct();
        
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getRights()
    {
        return $this->rights;
    }

    public function addRight(Right $right)
    {
        $this->rights[] = $right;
    }

}

?>
