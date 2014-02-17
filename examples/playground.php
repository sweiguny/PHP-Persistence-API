<?php

echo "<h2>Please see source code for explanation!</h2>";

/**
 * Retrieve all roles with their rights. Note that the table must correspond to @table(name=<name>) or the short classname.
 * As first parameter the querystring is passed and as second the fully qualified classname.
 * 
 * The rights are loaded eagerly.
 */
$query = new \PPA\core\query\TypedQuery("SELECT * FROM `role`", "\\PPA\\examples\\entity\\Role");

echo "<b>All roles:</b>";
\PPA\prettyDump($query->getResultList());

echo "<hr>";

/**
 * Selecting all users with their roles with criteria.
 */
$query = new \PPA\core\query\TypedQuery("SELECT * FROM `user` WHERE role_id = 2", "\\PPA\\examples\\entity\\User");
/**
 * Getting just the first result.
 */
$result = $query->getSingeResult();

/**
 * The role is represented with a MockEntity. Because it is loaded lazily
 */
echo "<b>MockEntity of Role, due to lazy-loading:</b>";
\PPA\prettyDump(get_class($result->getRole()));

echo "<hr>";

/**
 * But calling a real existing method, it will output the real value of the role.
 */
echo "<b>Calling method getName() of MockEntity - MockEntity will be exchanged:</b>";
\PPA\prettyDump($result->getRole()->getName());

echo "<hr>";

/**
 * After this call, the MockEntity will be exchanged with a real Role.
 */
echo "<b>Real Entity of Role after exchange:</b>";
\PPA\prettyDump($result->getRole());


?>
