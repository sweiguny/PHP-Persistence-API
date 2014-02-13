<?php


//$query = new PPA\core\query\TypedQuery("update `role` set name = 'oida'", "blub");
//$query = new PPA\core\query\Query("select * from `role` ");
////\PPA\prettyDump($query->getSingeResult());

//$query = new PPA\core\query\TypedQuery("select * from `user` ", "\\PPA\\examples\\entity\\User");
//\PPA\prettyDump($query->getResultList());

//$query = new PPA\core\query\TypedQuery("select * from `role` ", "\\PPA\\examples\\entity\\Role");
//\PPA\prettyDump($query->getResultList());


$query = new PPA\core\query\TypedQuery("select * from `order` ", "\\PPA\\examples\\entity\\Order");
\PPA\prettyDump($query->getResultList());





?>
