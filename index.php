<?php

$startTime = microtime(true);


require_once './Bootstrap.php';

\PPA\Bootstrap::boot("mysql:dbname=ppa;host=127.0.0.1;charset=utf8", "ppa", "ppa");

//new \PPA\examples\entity\Right();

$query = new PPA\sql\Query("select * from `right`");
//$query = new PPA\sql\Query("select count(*) from `right`");

//$res = $query->getSingeResult();
//$res = $query->getSingeResult("\\PPA\\examples\\entity\\Right");
$res = $query->getResultList("\\PPA\\examples\\entity\\Right");

\PPA\prettyDump($res);
//$res[0]->deny();




$endTime = microtime(true);

echo '<pre>' . ($endTime - $startTime) . '</pre>';

?>
