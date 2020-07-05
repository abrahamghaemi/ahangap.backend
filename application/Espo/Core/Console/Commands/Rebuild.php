<?php


namespace Espo\Core\Console\Commands;

class Rebuild extends Base
{
    public function run()
    {
        $this->getContainer()->get('dataManager')->rebuild();
        echo "Rebuild has been done.\n";
    }
}
