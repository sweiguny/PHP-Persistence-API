<?php

namespace PPA\examples;

use PPA\core\iPPA_Logger;

class Logger implements iPPA_Logger {

    public function log($logCode, $message) {
        \PPA\prettyDump("(" . $logCode . ") " . $message);
    }

}

?>
