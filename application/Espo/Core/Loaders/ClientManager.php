<?php


namespace Espo\Core\Loaders;

class ClientManager extends Base
{
    public function load()
    {
        return new \Espo\Core\Utils\ClientManager(
            $this->getContainer()->get('config'),
            $this->getContainer()->get('themeManager'),
            $this->getContainer()->get('metadata')
        );
    }
}
