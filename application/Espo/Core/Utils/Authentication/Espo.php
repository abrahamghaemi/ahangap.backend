<?php


namespace Espo\Core\Utils\Authentication;

use \Espo\Core\Exceptions\Error;

class Espo extends Base
{
    public function login($username, $password, \Espo\Entities\AuthToken $authToken = null, $params = [], $request)
    {
        if (!$password) return;

        if ($authToken) {
            $hash = $authToken->get('hash');
        } else {
            $hash = $this->getPasswordHash()->hash($password);
        }

        $user = $this->getEntityManager()->getRepository('User')->findOne([
            'whereClause' => [
                'userName' => $username,
                'password' => $hash,
                'type!=' => ['api', 'system']
            ]
        ]);

        return $user;
    }
}
