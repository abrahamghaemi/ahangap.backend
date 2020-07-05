<?php


namespace Espo\Core\ORM;

class RepositoryFactory extends \Espo\ORM\RepositoryFactory
{
    protected $defaultRepositoryClassName = '\\Espo\\Core\\ORM\\Repositories\\RDB';

    public function create($name)
    {
        $repository = parent::create($name);

        $dependencyList = $repository->getDependencyList();
        foreach ($dependencyList as $name) {
            $repository->inject($name, $this->entityManager->getContainer()->get($name));
        }
        return $repository;
    }
}
