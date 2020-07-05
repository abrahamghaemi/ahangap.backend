<?php


if (substr(php_sapi_name(), 0, 3) != 'cli') exit;

include "bootstrap.php";

$arg = isset($_SERVER['argv'][1]) ? trim($_SERVER['argv'][1]) : '';

if ($arg == 'version' || $arg == '-v') {
    $app = new \Espo\Core\Application();
    die("Current version is " . $app->getContainer()->get('config')->get('version') . ".\n");
}

if (empty($arg)) {
    die("Upgrade package file is not specified.\n");
}

if (!file_exists($arg)) {
    die("Package file does not exist.\n");
}

$pathInfo = pathinfo($arg);
if (!isset($pathInfo['extension']) || $pathInfo['extension'] !== 'zip' || !is_file($arg)) {
    die("Unsupported package.\n");
}

$app = new \Espo\Core\Application();

$config = $app->getContainer()->get('config');
$entityManager = $app->getContainer()->get('entityManager');

$user = $entityManager->getEntity('User', 'system');
$app->getContainer()->setUser($user);

$upgradeManager = new \Espo\Core\UpgradeManager($app->getContainer());

echo "Current version is " . $config->get('version') . "\n";
echo "Starting upgrade process...\n";

try {
    $fileData = file_get_contents($arg);
    $fileData = 'data:application/zip;base64,' . base64_encode($fileData);

    $upgradeId = $upgradeManager->upload($fileData);
    $upgradeManager->install(['id' => $upgradeId]);
} catch (\Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}

try {
    $app = new \Espo\Core\Application();
    $app->runRebuild();
} catch (\Exception $e) {}

echo "Upgrade is complete. Current version is " . $config->get('version') . ". \n";
