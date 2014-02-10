<?php

require_once './Bootstrap.php';

\PPA\Bootstrap::boot("mysql:dbname=ppa;host=localhost;charset=utf8", "root", "lei6395derf");

//new \PPA\examples\entity\Right();
$query = new PPA\sql\Query("select * from `right`", "\PPA\examples\entity\Right");

\PPA\prettyDump($query->getResultList());

?>
