<?php

namespace PPA\examples\entity;

use PPA\core\Entity;

/**
 * @table(name="user")
 */
class User extends \PPA\core\HistoryEntity
{
    /**
     * @id
     * @column(name="id")
     */
    private $id;

    /**
     * @Column(name="username")
     * @track
     */
    private $username;

    /**
     * @Column(name="password")
     * @track
     */
    private $password;

    /**
     * @track
     * @Column(name="role_id");
     * @oneToOne(fetch="lazy", mappedBy = "_PPA_examples_entity_Role", cascade="all")
     */
    private $role;

    public function __construct($username, $password)
    {
        parent::__construct();
        
        $this->username = $username;
        $this->password = $password;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole(Role $role)
    {
        $this->role = $role;
    }
    
    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

}

?>
