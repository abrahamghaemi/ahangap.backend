<?php


namespace Espo\Core\Upgrades\Actions\Upgrade;

use Espo\Core\Exceptions\Error;

class Uninstall extends \Espo\Core\Upgrades\Actions\Base\Uninstall
{
    public function run($data)
    {
        throw new Error('The operation is not permitted.');
    }
}
