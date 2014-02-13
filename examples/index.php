<?php

$startTime = microtime(true);


require_once __DIR__ . '/../Bootstrap.php';

\PPA\Bootstrap::boot("mysql:dbname=ppa;host=127.0.0.1;charset=utf8", "ppa", "ppa");


include './playground.php';


$endTime = microtime(true);

echo '<pre>' . ($endTime - $startTime) . '</pre>';

?>
