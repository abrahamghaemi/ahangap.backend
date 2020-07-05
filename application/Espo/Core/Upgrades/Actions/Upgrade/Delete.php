<?php


namespace Espo\Core\Upgrades\Actions\Upgrade;

use Espo\Core\Exceptions\Error;

class Delete extends \Espo\Core\Upgrades\Actions\Base\Delete
{
    public function run($data)
    {
        throw new Error('The operation is not permitted.');
    }
}
