<?php

PPA\PPA::getInstance()->setLogger(new \PPA\examples\Logger());
$em = PPA\core\EntityManager::getInstance();


//$query = new PPA\core\query\TypedQuery("select * from `role` where name='sepp'", "\\PPA\\examples\\entity\\Role");
//
//$role = $query->getSingleResult();
//$role->setName("sepp");
//$role->addRight(new PPA\examples\entity\Right("test"));
//$role->addRight(new PPA\examples\entity\Right("test2"));
//PPA\prettyDump($role);
//
//$em->persist($role);

//$em->persist(new PPA\examples\entity\User());

$query = new \PPA\core\query\TypedQuery("SELECT * FROM `order` WHERE id = 1", "\\PPA\\examples\\entity\\Order");
$order = $query->getSingleResult();
PPA\prettyDump($order->getOrderPos());
echo count($order->getOrderPos());
$order->getOrderPos()[1]->setArticle("can");
//$order->getOrderPos()[1]->setArticle("knife");
PPA\prettyDump($order->getOrderPos());

$em->persist($order);

//$query = new \PPA\core\query\TypedQuery("SELECT * FROM `user` WHERE id = 1", "\\PPA\\examples\\entity\\User");
//$user  = $query->getSingleResult();

//$query = new \PPA\core\query\TypedQuery("SELECT * FROM `role` WHERE id = 1", "\\PPA\\examples\\entity\\Role");
//$role  = $query->getSingleResult();
//$user->setRole($role);

//\PPA\prettyDump($role->getRights());
//$em->persist($role);
//$em->remove($role->getRights()[1]);
//$em->remove($role);

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
