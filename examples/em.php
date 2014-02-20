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

//throw new \PPA\core\exception\TransactionException("test");

//$em->begin();
////$q = new PPA\core\query\TypedQuery("select * from `right` where id = 119", "\\PPA\\examples\\entity\\Right");
////$order = $q->getSingleResult();
//$order = new PPA\examples\entity\Right('simon');
////$order->addOrderpos(new PPA\examples\entity\OrderPosition("oida", 10));
////$order->addOrderpos(new PPA\examples\entity\OrderPosition("yunga", 20));
//
//$em->persist($order);
//$em->commit();
////
//$em->begin();
//$em->remove($order);
//$em->commit();
//PPA\prettyDump($role);

?>
