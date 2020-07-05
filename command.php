<?php


if (substr(php_sapi_name(), 0, 3) != 'cli') exit;

ob_start();

$command = isset($_SERVER['argv'][1]) ? trim($_SERVER['argv'][1]) : null;
if (empty($command)) exit;

include "bootstrap.php";
$app = new \Espo\Core\Application();
$result = $app->runCommand($command);

if (is_string($result)) {
    ob_end_clean();
    echo $result;
}
exit;
