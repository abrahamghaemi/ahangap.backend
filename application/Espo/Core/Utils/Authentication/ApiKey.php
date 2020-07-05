<?php


namespace Espo\Core\Utils\Authentication;

use \Espo\Core\Exceptions\Error;

class ApiKey extends Base
{
    public function login($username, $password, $authToken = null, $params = [], $request)
    {
        $apiKey = $username;

        $user = $this->getEntityManager()->getRepository('User')->findOne([
            'whereClause' => [
                'type' => 'api',
                'apiKey' => $apiKey,
                'authMethod' => 'ApiKey'
            ]
        ]);

        return $user;
    }
}
