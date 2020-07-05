<?php


namespace Espo\Core\Loaders;

class SelectManagerFactory extends Base
{
    public function load()
    {
        return new \Espo\Core\SelectManagerFactory(
            $this->getContainer()->get('entityManager'),
            $this->getContainer()->get('user'),
            $this->getContainer()->get('acl'),
            $this->getContainer()->get('aclManager'),
            $this->getContainer()->get('metadata'),
            $this->getContainer()->get('config'),
            $this->getContainer()->get('fieldManagerUtil'),
            $this->getContainer()->get('injectableFactory')
        );
    }
}
