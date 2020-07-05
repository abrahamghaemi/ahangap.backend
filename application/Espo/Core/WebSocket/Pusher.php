<?php


namespace Espo\Core\WebSocket;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class Pusher implements WampServerInterface
{
    private $categoryList;

    protected $categoriesData;

    protected $isDebugMode = false;

    protected $connectionIdUserIdMap = [];

    protected $userIdConnectionIdListMap = [];

    protected $connectionIdTopicIdListMap = [];

    protected $connections = [];

    protected $topicHash = [];

    private $phpExecutablePath;

    public function __construct(array $categoriesData = [], ?string $phpExecutablePath = null, bool $isDebugMode = false)
    {
        $this->categoryList = array_keys($categoriesData);
        $this->categoriesData = $categoriesData;

        $this->phpExecutablePath = $phpExecutablePath ?: (new \Symfony\Component\Process\PhpExecutableFinder)->find();
        $this->isDebugMode = $isDebugMode;
    }

    public function onSubscribe(ConnectionInterface $connection, $topic)
    {
        $topicId = $topic->getId();
        if (!$topicId) return;

        if (!$this->isTopicAllowed($topicId)) return;

        $connectionId = $connection->resourceId;

        $userId = $this->getUserIdByConnection($connection);
        if (!$userId) return;

        if (!isset($this->connectionIdTopicIdListMap[$connectionId])) $this->connectionIdTopicIdListMap[$connectionId] = [];

        $checkCommand = $this->getAccessCheckCommandForTopic($connection, $topic);

        if ($checkCommand) {
            $checkResult = shell_exec($checkCommand);
            if ($checkResult !== 'true') {
                if ($this->isDebugMode) $this->log("check access failed for topic {$topicId} for user {$userId}");
                return;
            }
        }

        if (!in_array($topicId, $this->connectionIdTopicIdListMap[$connectionId])) {
            if ($this->isDebugMode) $this->log("{$connectionId}: add topic {$topicId} for user {$userId}");
            $this->connectionIdTopicIdListMap[$connectionId][] = $topicId;
        }

        $this->topicHash[$topicId] = $topic;
    }

    public function onUnSubscribe(ConnectionInterface $connection, $topic)
    {
        $topicId = $topic->getId();
        if (!$topicId) return;

        if (!$this->isTopicAllowed($topicId)) return;

        $connectionId = $connection->resourceId;

        $userId = $this->getUserIdByConnection($connection);
        if (!$userId) return;

        if (isset($this->connectionIdTopicIdListMap[$connectionId])) {
            $index = array_search($topicId, $this->connectionIdTopicIdListMap[$connectionId]);
            if ($index !== false) {
                if ($this->isDebugMode) $this->log("{$connectionId}: remove topic {$topicId} for user {$userId}");
                unset($this->connectionIdTopicIdListMap[$connectionId][$index]);
                $this->connectionIdTopicIdListMap[$connectionId][$index] = array_values($this->connectionIdTopicIdListMap[$connectionId][$index]);
            }
        }
    }

    protected function getParamsFromTopicId(string $topicId) : array
    {
        $arr = explode('.', $topicId);
        $category = $arr[0];

        $params = [];

        if (array_key_exists('paramList', $this->categoriesData[$category])) {
            foreach ($this->categoriesData[$category]['paramList'] as $i => $item) {
                if (isset($arr[$i + 1])) {
                    $params[$item] = $arr[$i + 1];
                } else {
                    $params[$item] = '';
                }
            }
        }

        return $params;
    }

    protected function getAccessCheckCommandForTopic(ConnectionInterface $connection, $topic) : ?string
    {
        $topicId = $topic->getId();

        $params = $this->getParamsFromTopicId($topicId);
        $params['userId'] = $this->getUserIdByConnection($connection);
        if (!$params['userId']) {
            $connection->close();
            return null;
        }

        $category = $this->getTopicCategory($topic);
        if (!array_key_exists('accessCheckCommand', $this->categoriesData[$category])) return null;

        $command = $this->phpExecutablePath . " command.php " . $this->categoriesData[$category]['accessCheckCommand'];
        foreach ($params as $key => $value) {
            $command = str_replace(':' . $key, $value, $command);
        }

        return $command;
    }

    protected function getTopicCategory($topic)
    {
        list($category) = explode('.', $topic->getId());
        return $category;
    }

    protected function isTopicAllowed($topicId)
    {
        list($category) = explode( '.', $topicId);

        return in_array($topicId, $this->categoryList) || in_array($category, $this->categoryList);
    }

    protected function getConnectionIdListByUserId($userId)
    {
        if (!isset($this->userIdConnectionIdListMap[$userId])) return [];
        return $this->userIdConnectionIdListMap[$userId];
    }

    protected function getUserIdByConnection(ConnectionInterface $connection)
    {
        if (!isset($this->connectionIdUserIdMap[$connection->resourceId])) return;
        return $this->connectionIdUserIdMap[$connection->resourceId];
    }

    protected function subscribeUser(ConnectionInterface $connection, $userId)
    {
        $resourceId = $connection->resourceId;

        $this->connectionIdUserIdMap[$resourceId] = $userId;

        if (!isset($this->userIdConnectionIdListMap[$userId])) $this->userIdConnectionIdListMap[$userId] = [];

        if (!in_array($resourceId, $this->userIdConnectionIdListMap[$userId])) {
            $this->userIdConnectionIdListMap[$userId][] = $resourceId;
        }

        $this->connections[$resourceId] = $connection;

        if ($this->isDebugMode) $this->log("{$resourceId}: user {$userId} subscribed");
    }

    protected function unsubscribeUser(ConnectionInterface $connection, $userId)
    {
        $resourceId = $connection->resourceId;

        unset($this->connectionIdUserIdMap[$resourceId]);

        if (isset($this->userIdConnectionIdListMap[$userId])) {
            $index = array_search($resourceId, $this->userIdConnectionIdListMap[$userId]);
            if ($index !== false) {
                unset($this->userIdConnectionIdListMap[$userId][$index]);
                $this->userIdConnectionIdListMap[$userId] = array_values($this->userIdConnectionIdListMap[$userId]);
            }
        }

        if ($this->isDebugMode) $this->log("{$resourceId}: user {$userId} unsubscribed");
    }

    public function onOpen(ConnectionInterface $connection)
    {
        if ($this->isDebugMode) $this->log("{$connection->resourceId}: open");

        $query = $connection->httpRequest->getUri()->getQuery();
        $params = \GuzzleHttp\Psr7\parse_query($query ?: '');
        if (empty($params['userId']) || empty($params['authToken'])) {
            $this->closeConnection($connection);
            return;
        }

        $authToken = preg_replace('/[^a-zA-Z0-9]+/', '', $params['authToken']);
        $userId = $params['userId'];

        $result = $this->getUserIdByAuthToken($authToken);

        if (empty($result)) {
            $this->closeConnection($connection);
            return;
        }

        if ($result !== $userId) {
            $this->closeConnection($connection);
            return;
        }

        $this->subscribeUser($connection, $userId);
    }

    private function getUserIdByAuthToken($authToken)
    {
        return shell_exec($this->phpExecutablePath . " command.php AuthTokenCheck " . $authToken);
    }

    protected function closeConnection(ConnectionInterface $connection)
    {
        $userId = $this->getUserIdByConnection($connection);
        if ($userId) {
            $this->unsubscribeUser($connection, $userId);
        }

        $connection->close();
    }

    public function onClose(ConnectionInterface $connection)
    {
        if ($this->isDebugMode) $this->log("{$connection->resourceId}: close");

        $userId = $this->getUserIdByConnection($connection);

        if ($userId) {
            $this->unsubscribeUser($connection, $userId);
        }

        unset($this->connections[$connection->resourceId]);
    }

    public function onCall(ConnectionInterface $connection, $id, $topic, array $params)
    {
        $connection->callError($id, $topic, 'You are not allowed to make calls')->close();
    }

    public function onPublish(ConnectionInterface $connection, $topic, $event, array $exclude, array $eligible)
    {
        $topicId = $topic->getId();
        $connection->close();
    }

    public function onError(ConnectionInterface $connection, \Exception $e)
    {
    }

    public function onMessageReceive($dataString)
    {
        $data = json_decode($dataString);

        if (!property_exists($data, 'topicId')) return;

        $userId = $data->userId ?? null;
        $topicId = $data->topicId;

        if (!$topicId) return;
        if (!$this->isTopicAllowed($topicId)) return;

        if ($userId) {
            foreach ($this->getConnectionIdListByUserId($userId) as $connectionId) {
                if (!isset($this->connections[$connectionId])) continue;
                if (!isset($this->connectionIdTopicIdListMap[$connectionId])) continue;

                $connection = $this->connections[$connectionId];

                if (in_array($topicId, $this->connectionIdTopicIdListMap[$connectionId])) {
                    if ($this->isDebugMode) $this->log("send {$topicId} for connection {$connectionId}");
                    $connection->event($topicId, $data);
                }
            }

            if ($this->isDebugMode) $this->log("message {$topicId} for user {$userId}");
        } else {
            $topic = $this->topicHash[$topicId] ?? null;
            if ($topic) {
                $topic->broadcast($data);
                if ($this->isDebugMode) $this->log("send {$topicId} to all");
            }

            if ($this->isDebugMode) $this->log("message {$topicId} for all");
        }
    }

    protected function log($msg)
    {
        echo "[" . date('Y-m-d H:i:s') . "] " . $msg . "\n";
    }
}
