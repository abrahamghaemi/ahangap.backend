<?php


namespace Espo\Core\Console\Commands;

class AuthTokenCheck extends Base
{
    public function run($options, $flagList, $argumentList)
    {
        $token = $argumentList[0] ?? null;
        if (empty($token)) return;

        $entityManager = $this->getContainer()->get('entityManager');

        $authToken = $entityManager->getRepository('AuthToken')->where([
            'token' => $token,
            'isActive' => true,
        ])->findOne();

        if (!$authToken) return;
        if (!$authToken->get('userId')) return;

        $userId = $authToken->get('userId');

        $user = $entityManager->getEntity('User', $userId);
        if (!$user) return;

        return $user->id;
    }
}
