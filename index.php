<?php

$startTime = microtime(true);


require_once './Bootstrap.php';

\PPA\Bootstrap::boot("mysql:dbname=ppa;host=127.0.0.1;charset=utf8", "ppa", "ppa");

//new \PPA\examples\entity\Right();

//$analyzer = new \PPA\EntityAnalyzer("\\PPA\\examples\\entity\\User");
//\PPA\prettyDump($analyzer->getPersistenceProperties());

$query = new PPA\sql\Query("select * from `user`");
////$query = new PPA\sql\Query("select count(*) from `right`");
//
////$res = $query->getSingeResult();
////$res = $query->getSingeResult("\\PPA\\examples\\entity\\Right");
$res = $query->getResultList("\\PPA\\examples\\entity\\User");
//
\PPA\prettyDump($res);
//\PPA\prettyDump(PPA\EntityMap::getInstance());
//$res[0]->deny();

//$x = new PPA\examples\entity\User();
//\PPA\prettyDump($x->getProperties());


$endTime = microtime(true);

echo '<pre>' . ($endTime - $startTime) . '</pre>';

?>
