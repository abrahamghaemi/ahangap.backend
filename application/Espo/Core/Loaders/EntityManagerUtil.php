<?php


namespace Espo\Core\Loaders;

class EntityManagerUtil extends Base
{
    public function load()
    {
        $entityManager = new \Espo\Core\Utils\EntityManager(
            $this->getContainer()->get('metadata'),
            $this->getContainer()->get('language'),
            $this->getContainer()->get('fileManager'),
            $this->getContainer()->get('config'),
            $this->getContainer()
        );

        return $entityManager;
    }
}

