<?php


if (substr(php_sapi_name(), 0, 3) != 'cli') die('ClearCache can be run only via CLI.');

include "bootstrap.php";

$app = new \Espo\Core\Application();
$app->runClearCache();
