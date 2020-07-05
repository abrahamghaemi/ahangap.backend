<?php


namespace Espo\Core\WebSocket;

class Submission
{
    protected $config;

    public function __construct(\Espo\Core\Utils\Config $config)
    {
        $this->config = $config;
    }

    public function submit(string $topic, ?string $userId = null, $data = null)
    {
        if (!$data) $data = (object) [];

        $dsn = $this->config->get('webSocketSubmissionDsn', 'tcp://localhost:5555');

        if ($userId) {
            $data->userId = $userId;
        }
        $data->topicId = $topic;

        try {
            $context = new \ZMQContext();
            $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'my pusher');
            $socket->connect($dsn);

            $socket->send(json_encode($data));

            $socket->setSockOpt(\ZMQ::SOCKOPT_LINGER, 1000);
            $socket->disconnect($dsn);
        } catch (\Throwable $e) {
            $GLOBALS['log']->error("WebSocketSubmission: " . $e->getMessage());
        }
    }
}
