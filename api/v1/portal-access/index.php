<?php


require_once('../../../bootstrap.php');

if (!empty($_GET['portalId'])) {
    $portalId = $_GET['portalId'];
} else {
    $portalId = explode('/', $_SERVER['REQUEST_URI'])[count(explode('/', $_SERVER['SCRIPT_NAME'])) - 1];
}

$app = new \Espo\Core\Portal\Application($portalId);
$app->run();
