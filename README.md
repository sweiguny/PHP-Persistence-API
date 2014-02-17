PHP Persistence API
======

The PHP Persistence API (`PPA`) is an Interface for PHP-Applications to access Object-Relational data.

**Features:**
- Easy to embed in your project
- Configure entities via [annotations](https://github.com/sweiguny/PHP-Persistence-API/wiki/Annotations-&-Parameters)
  - Relations
    - OneToOne
    - OneToMany
    - ManyToMany
- [TypedQueries](https://github.com/sweiguny/PHP-Persistence-API/wiki/TypedQuery)
- Eager- & Lazy-loading
- A neat [WIKI](https://github.com/sweiguny/PHP-Persistence-API/wiki)

**Features in spe:**
- Transactions
- CRUD
- Inheritance

***

##Examples:

**Configuring your entities:**

    use PPA\core\Entity;

    /**
     * @table(name="user")
     */
    class User extends Entity {

        /**
         * @id
         * @column(name="id")
         */
        private $id;

        /**
         * @Column(name="username")
         */
        private $username;

        /**
         * @Column(name="password")
         */
        private $password;

        /**
         * @Column(name="role_id");
         * @oneToOne(fetch="lazy", mappedBy = "_PPA_examples_entity_Role")
         */
        private $role;

        public function getRole() {
            return $this->role;
        }
    }

***

**Retrieving data:**

    use PPA\core\query\TypedQuery;
    
    // A TypedQuery can automatically resolve all the relations and give an appropriate output.
    $query = new TypedQuery("SELECT * FROM `role` WHERE id = 2", "\\PPA\\examples\\entity\\Role");
    $query->getSingleResult();

    Returns:
    [0] => PPA\examples\entity\Role Object
        (
            [id:PPA\examples\entity\Role:private] => 1
            [name:PPA\examples\entity\Role:private] => admin
            [rights:PPA\examples\entity\Role:private] => Array
                (
                    [0] => PPA\examples\entity\Right Object
                        (
                            [id:PPA\examples\entity\Right:private] => 3
                            [desc:PPA\examples\entity\Right:private] => ch-pw
                        )
                    [1] => PPA\examples\entity\Right Object
                        (
                            [id:PPA\examples\entity\Right:private] => 1
                            [desc:PPA\examples\entity\Right:private] => login
                        )
                    [2] => PPA\examples\entity\Right Object
                        (
                            [id:PPA\examples\entity\Right:private] => 2
                            [desc:PPA\examples\entity\Right:private] => logout
                        )
                    [3] => PPA\examples\entity\Right Object
                        (
                            [id:PPA\examples\entity\Right:private] => 5
                            [desc:PPA\examples\entity\Right:private] => create_order
                        )
                    [4] => PPA\examples\entity\Right Object
                        (
                            [id:PPA\examples\entity\Right:private] => 4
                            [desc:PPA\examples\entity\Right:private] => delete_order
                        )
                )
        )

***

For more examples, please see the [WIKI](https://github.com/sweiguny/PHP-Persistence-API/wiki).