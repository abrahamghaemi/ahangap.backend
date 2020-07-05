<?php


namespace Espo\Core\Loaders;

class ExternalAccountClientManager extends Base
{
    public function load()
    {
        return new \Espo\Core\ExternalAccount\ClientManager(
            $this->getContainer()->get('entityManager'),
            $this->getContainer()->get('metadata'),
            $this->getContainer()->get('config')
        );
    }
}
