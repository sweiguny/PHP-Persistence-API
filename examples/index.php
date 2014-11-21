<?php

$startTime = microtime(true);


require_once __DIR__ . '/../PPA.php';

\PPA\PPA::init("mysql:dbname=ppa;host=127.0.0.1;charset=utf8", "ppa", "ppa", array(
//    \PPA\PPA::OPTION_DEFAULT_CASCADE_TYPE => "all",
    \PPA\PPA::OPTION_LOG_RETRIEVES => true,
    \PPA\PPA::OPTION_LOG_NOTIFICATIONS => true
));

//\PPA\PPA::printOptions();

//include './playground.php';
include './em.php';


$endTime = microtime(true);

echo '<pre>Execution time: ' . ($endTime - $startTime) . ' ms</pre>';

?>
