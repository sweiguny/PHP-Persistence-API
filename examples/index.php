<?php

$startTime = microtime(true);


require_once __DIR__ . '/../PPA.php';

\PPA\PPA::init("mysql:dbname=ppa;host=127.0.0.1;charset=utf8", "ppa", "ppa");


//include './playground.php';
include './em.php';


$endTime = microtime(true);

echo '<pre>Execution time: ' . ($endTime - $startTime) . ' ms</pre>';

?>