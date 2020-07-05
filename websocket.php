<?php


if (substr(php_sapi_name(), 0, 3) != 'cli') die('WebSocket can be run only via CLI.');

include "bootstrap.php";

$app = new \Espo\Core\Application();
$config = $app->getContainer()->get('config');

$categoriesData = $app->getContainer()->get('metadata')->get(['app', 'webSocket', 'categories'], []);

$phpExecutablePath = $config->get('phpExecutablePath');
$isDebugMode = (bool) $config->get('webSocketDebugMode');

$loop = \React\EventLoop\Factory::create();
$pusher = new \Espo\Core\WebSocket\Pusher($categoriesData, $phpExecutablePath, $isDebugMode);

$context = new \React\ZMQ\Context($loop);
$pull = $context->getSocket(\ZMQ::SOCKET_PULL);
$pull->bind('tcp://127.0.0.1:5555');
$pull->on('message', [$pusher, 'onMessageReceive']);


$useSecureServer = $config->get('webSocketUseSecureServer');

$port = $config->get('webSocketPort');
if (!$port) {
    $port = $useSecureServer ? '8443' : '8080';
}

$webSocket = new \React\Socket\Server('0.0.0.0:'.$port, $loop);

if ($useSecureServer) {
    $sslParams = [
        'local_cert' => $config->get('webSocketSslCertificateFile'),
        'allow_self_signed' => $config->get('webSocketSslAllowSelfSigned', false),
        'verify_peer' => false,
    ];
    if ($config->get('webSocketSslCertificatePassphrase')) {
        $sslParams['passphrase'] = $config->get('webSocketSslCertificatePassphrase');
    }
    if ($config->get('webSocketSslCertificateLocalPrivateKey')) {
        $sslParams['local_pk'] = $config->get('webSocketSslCertificateLocalPrivateKey');
    }
    $webSocket = new \React\Socket\SecureServer($webSocket, $loop, $sslParams);
}

$webServer = new \Ratchet\Server\IoServer(
    new \Ratchet\Http\HttpServer(
        new \Ratchet\WebSocket\WsServer(
            new \Ratchet\Wamp\WampServer($pusher)
        )
    ),
    $webSocket
);

$loop->run();
