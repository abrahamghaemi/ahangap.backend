<?php


namespace Espo\Core\Loaders;

class EmailFilterManager extends Base
{
    public function load()
    {
        $emailFilterManager = new \Espo\Core\Utils\EmailFilterManager(
            $this->getContainer()->get('entityManager')
        );

        return $emailFilterManager;
    }
}

