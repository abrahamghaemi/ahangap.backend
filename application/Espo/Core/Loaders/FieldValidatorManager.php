<?php


namespace Espo\Core\Loaders;

class FieldValidatorManager extends Base
{
    public function load()
    {
        return new \Espo\Core\Utils\FieldValidatorManager(
            $this->getContainer()->get('metadata'),
            $this->getContainer()->get('fieldManagerUtil')
        );
    }
}
