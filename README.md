PHP Persistence API
======
[![Build Status](https://travis-ci.org/sweiguny/PHP-Persistence-API.svg?branch=master)](https://travis-ci.org/sweiguny/PHP-Persistence-API)

The PHP Persistence API (`PPA`) is an Interface for PHP-Applications to access Object-Relational data.

## Note: this project currently underlies a huge refactoring process.

***

#### Features:
- [Easy to embed in your project](https://github.com/sweiguny/PHP-Persistence-API/wiki/Embedding-PPA)
- Configure entities via [annotations](https://github.com/sweiguny/PHP-Persistence-API/wiki/Annotations-&-Parameters)
  - Relations
    - OneToOne
    - OneToMany
    - ManyToMany
- [TypedQueries](https://github.com/sweiguny/PHP-Persistence-API/wiki/TypedQuery) and [PreparedQueries](https://github.com/sweiguny/PHP-Persistence-API/wiki/PreparedQuery).
- CRUD
  - Transactions
- [Eager & Lazy Loading](https://github.com/sweiguny/PHP-Persistence-API/wiki/Eager-and-Lazy-Loading)
- A neat [WIKI](https://github.com/sweiguny/PHP-Persistence-API/wiki)

**Features in spe:**
- Inheritance

***

### Examples:

**Configuring your entities:**
```php
namespace PPA\examples\entity;
use PPA\core\Entity;

/**
 * @table(name="role")
 */
class Role extends Entity {

    /**
     * @id
     * @column(name="id")
     */
    private $id;

    /** @column(name="name") */
    private $name;

    /**
     * @manyToMany(fetch = "eager", mappedBy = "_PPA_examples_entity_Right")
     * @joinTable(name = "role2right", column = "role_id", x_column = "right_id")
     */
    private $rights = array();
}
```
***

**Retrieving data:**

A TypedQuery can automatically resolve all the relations and give an appropriate output.
```php
$query = new \PPA\core\query\TypedQuery("SELECT * FROM `role` WHERE id = 1", "\\PPA\\examples\\entity\\Role");
$query->getSingleResult();

Returns:
PPA\examples\entity\Role Object
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
        )
)
```
