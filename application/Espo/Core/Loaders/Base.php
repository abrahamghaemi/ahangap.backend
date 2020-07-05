<?php


namespace Espo\Core\Loaders;

abstract class Base implements \Espo\Core\Interfaces\Loader
{
    private $container;

    public function __construct(\Espo\Core\Container $container)
    {
        $this->container = $container;
    }

    protected function getContainer()
    {
        return $this->container;
    }
}
