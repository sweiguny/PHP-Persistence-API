<?php

use PPA\tests\bootstrap\ConnectionProviderForTestEnvironment;

require_once __DIR__ . "/../../vendor/autoload.php";

define("PPA_TEST_BOOTSTRAP_PATH", __DIR__);
define("PPA_TEST_CONFIG_PATH", __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "config");

ConnectionProviderForTestEnvironment::init();

?>