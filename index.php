<?php

use function PPA\dbal\query\builder\AST\expressions\functions\aggregate\Count;
use function PPA\dbal\query\builder\AST\expressions\functions\aggregate\Sum;
//use function This\is\crazy\MyTestClass;
//use This\is\crazy\MyTestClass;

require_once './vendor/autoload.php';

echo "<p>Welcome to the PHP Persistence API!</p>";
echo "<p><a href='examples/index.php'>examples</a></p>";

//new \PPA\dbal\drivers\concrete\MySQLDriver([]);
//include './MyTestClass.php';
//include './src/dbal/query/builder/AST/expressions/functions/aggregate/Sum.php';

//MyTestClass();

Sum();
Count();

?>
