<?php


namespace Espo\Core\Loaders;

class ConsoleCommandManager extends Base
{
    public function load()
    {
        return new \Espo\Core\Console\CommandManager(
            $this->getContainer()
        );
    }
}
