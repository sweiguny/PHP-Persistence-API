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

//$query = new PPA\core\query\TypedQuery("select * from `role` where id=1", "\\PPA\\examples\\entity\\Role");
//$role = $query->getSingleResult();


//$role = new PPA\examples\entity\Role("neu");
//
//$role->addRight(new \PPA\examples\entity\Right("test"));
//$role->addRight(new \PPA\examples\entity\Right("test"));
//$em->persist($role);

$order = new \PPA\examples\entity\Order('simon');
$order->addOrderpos(new PPA\examples\entity\OrderPosition("oida", 10));
$order->addOrderpos(new PPA\examples\entity\OrderPosition("yunga", 20));

$em->persist($order);


//PPA\prettyDump($role);

?>
