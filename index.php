<?php

require_once './vendor/autoload.php';

echo "<p>Welcome to the PHP Persistence API!</p>";
echo "<p><a href='examples/index.php'>examples</a></p>";

new \PPA\dbal\drivers\concrete\MySQLDriver([]);

?>
