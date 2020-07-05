<?php


namespace Espo\Core\Console\Commands;

class ClearCache extends Base
{
    public function run()
    {
        $this->getContainer()->get('dataManager')->clearCache();
        echo "Cache has been cleared.\n";
    }
}
