<?php

use PPA\tests\bootstrap\DynamicConfig;

require_once implode(DIRECTORY_SEPARATOR, [__DIR__, "..", "bootstrap", "include.php"]);

//print_r($argv);
$noExclusions = isset($argv[1]) && $argv[1] == "all";

$config = new DynamicConfig();
$config->writeDynamicConfig($noExclusions);

?>
