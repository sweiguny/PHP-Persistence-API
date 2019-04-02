<?php

define("PPA_TEST_BOOTSTRAP_PATH", __DIR__);
define("PPA_TEST_CONFIG_PATH", implode(DIRECTORY_SEPARATOR, [PPA_TEST_BOOTSTRAP_PATH, "..", "config"]));

require_once implode(DIRECTORY_SEPARATOR, [PPA_TEST_BOOTSTRAP_PATH, "..", "..", "vendor", "autoload.php"]);

?>