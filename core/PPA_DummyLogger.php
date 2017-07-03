<?php

namespace PPA\core;

use PPA\core\iPPA_Logger;

class PPA_DummyLogger implements iPPA_Logger
{

    public function log($logCode, $message) { }

}

?>
