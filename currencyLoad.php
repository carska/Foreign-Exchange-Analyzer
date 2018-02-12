<?php

include 'currencyHandler.php';
$handler = new currencyHandler();
$result = $handler->doWork($_REQUEST["date"]);
echo $result;
?>
