<?php

$em = PPA\core\EntityManager::getInstance();


//$query = new PPA\core\query\TypedQuery("select * from `user` where id=1", "\\PPA\\examples\\entity\\User");
//
//$user = $query->getSingleResult();
//$user->setRole(new PPA\examples\entity\Role("test"));
//PPA\prettyDump($user);
//
//$em->persist($user);

//$em->persist(new PPA\examples\entity\User());

$query = new PPA\core\query\TypedQuery("select * from `role` where id=1", "\\PPA\\examples\\entity\\Role");
$role = $query->getSingleResult();

$role->addRight(new \PPA\examples\entity\Right("test"));
$role->addRight(new \PPA\examples\entity\Right("test"));

$em->persist($role);

//PPA\prettyDump($role);

?>
