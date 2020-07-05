<?php


if (substr(php_sapi_name(), 0, 3) != 'cli') die('Rebuild can be run only via CLI.');

include "bootstrap.php";

$app = new \Espo\Core\Application();
$app->runRebuild();
