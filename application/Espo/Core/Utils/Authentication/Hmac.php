<?php


namespace Espo\Core\Utils\Authentication;

use \Espo\Core\Exceptions\Error;

class Hmac extends Base
{
    public function login($username, $password, $authToken = null, $params = [], $request)
    {
        $apiKey = $username;
        $hash = $password;

        $user = $this->getEntityManager()->getRepository('User')->findOne([
            'whereClause' => [
                'type' => 'api',
                'apiKey' => $apiKey,
                'authMethod' => 'Hmac'
            ]
        ]);

        if (!$user) return;

        if ($user) {
            $apiKeyUtil = new \Espo\Core\Utils\ApiKey($this->getConfig());
            $secretKey = $apiKeyUtil->getSecretKeyForUserId($user->id);
            if (!$secretKey) return;

            $string = $request->getMethod() . ' ' . $request->getResourceUri();

            if ($hash === \Espo\Core\Utils\ApiKey::hash($secretKey, $string)) {
                return $user;
            }

            return;
        }

        return $user;
    }
}
