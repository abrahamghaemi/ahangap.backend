<?php


namespace Espo\Core\Utils;

class ApiKey
{
    private $config;

    public function __construct(\Espo\Core\Utils\Config $config)
    {
        $this->config = $config;
    }

    protected function getConfig()
    {
        return $this->config;
    }

    public static function hash($secretKey, $string = '')
    {
        return hash_hmac('sha256', $string, $secretKey, true);
    }

    public function getSecretKeyForUserId($id)
    {
        $apiSecretKeys = $this->getConfig()->get('apiSecretKeys');
        if (!$apiSecretKeys) return;
        if (!is_object($apiSecretKeys)) return;
        if (!isset($apiSecretKeys->$id)) return;
        return $apiSecretKeys->$id;
    }

    public function storeSecretKeyForUserId($id, $secretKey)
    {
        $apiSecretKeys = $this->getConfig()->get('apiSecretKeys');
        if (!is_object($apiSecretKeys)) {
            $apiSecretKeys = (object)[];
        }
        $apiSecretKeys->$id = $secretKey;

        $this->getConfig()->set('apiSecretKeys', $apiSecretKeys);
        $this->getConfig()->save();
    }

    public function removeSecretKeyForUserId($id)
    {
        $apiSecretKeys = $this->getConfig()->get('apiSecretKeys');
        if (!is_object($apiSecretKeys)) {
            $apiSecretKeys = (object)[];
        }
        unset($apiSecretKeys->$id);

        $this->getConfig()->set('apiSecretKeys', $apiSecretKeys);
        $this->getConfig()->save();
    }
}
