<?php


namespace Espo\Core\Loaders;

class FormulaManager extends Base
{
    public function load()
    {
        $formulaManager = new \Espo\Core\Formula\Manager(
            $this->getContainer(),
            $this->getContainer()->get('metadata')
        );

        return $formulaManager;
    }
}
