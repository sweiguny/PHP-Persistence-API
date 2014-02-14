<?php


//$query = new PPA\core\query\TypedQuery("update `role` set name = 'oida'", "blub");
//$query = new PPA\core\query\Query("select * from `role` ");
////\PPA\prettyDump($query->getSingeResult());

$query = new PPA\core\query\TypedQuery("select * from `user` ", "\\PPA\\examples\\entity\\User");
$result = $query->getResultList();
//\PPA\prettyDump($result);

echo $result[0]->getRole()->getName();
$result = $result[0]->getRole();


echo $result->getRights()->func();

foreach ($result->getRights() as $right) {
    PPA\prettyDump($result->getRights());
    \PPA\prettyDump($right);
}

//\PPA\prettyDump($result->getRights()[0]);


//echo $result;
//
//\PPA\prettyDump($result);

//$query = new PPA\core\query\TypedQuery("select * from `role` ", "\\PPA\\examples\\entity\\Role");
//\PPA\prettyDump($query->getResultList());


//$query = new PPA\core\query\TypedQuery("select * from `order` ", "\\PPA\\examples\\entity\\Order");
//\PPA\prettyDump($query->getResultList());



?>
