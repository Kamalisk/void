<?php

require "lessc.inc.php";

$less = new lessc;

header("Content-type: text/css");
echo $less->compileFile("void.less");

?>