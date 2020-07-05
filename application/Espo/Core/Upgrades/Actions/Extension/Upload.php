<?php


namespace Espo\Core\Upgrades\Actions\Extension;

class Upload extends \Espo\Core\Upgrades\Actions\Base\Upload
{
    protected function checkDependencies($dependencyList)
    {
        return $this->getHelper()->checkDependencies($dependencyList);
    }
}
