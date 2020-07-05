<?php


namespace Espo\Repositories;

use \Espo\ORM\Entity;

class UserData extends \Espo\Core\ORM\Repositories\RDB
{
    public function getByUserId(string $userId) : ?Entity
    {
        $userData = $this->where(['userId' => $userId])->findOne();

        if ($userData) return $userData;

        $user = $this->getEntityManager()->getRepository('User')->getById($userId);

        if (!$user) return null;

        $userData = $this->getNew();
        $userData->set('userId', $userId);

        $this->save($userData, [
            'silent' => true,
            'skipHooks' => true,
        ]);

        return $userData;
    }
}
