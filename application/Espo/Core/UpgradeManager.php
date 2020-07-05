<?php


namespace Espo\Core;

use Espo\Core\Exceptions\Error;

class UpgradeManager extends Upgrades\Base
{
    protected $name = 'Upgrade';

    protected $params = array(
        'packagePath' => 'data/upload/upgrades',
        'backupPath' => 'data/.backup/upgrades',

        'scriptNames' => array(
            'before' => 'BeforeUpgrade',
            'after' => 'AfterUpgrade',
        ),

        'customDirNames' => array(
            'before' => 'beforeUpgradeFiles',
            'after' => 'afterUpgradeFiles',
            'vendor' => 'vendorFiles',
        )
    );
}
