<?php


namespace Espo\Core\Formula\Functions\RecordGroup;

use \Espo\ORM\Entity;
use \Espo\Core\Exceptions\Error;

class CountType extends \Espo\Core\Formula\Functions\Base
{
    protected function init()
    {
        $this->addDependency('entityManager');
        $this->addDependency('selectManagerFactory');
    }

    public function process(\StdClass $item)
    {
        if (!property_exists($item, 'value')) {
            throw new Error();
        }

        if (!is_array($item->value)) {
            throw new Error();
        }

        if (count($item->value) < 1) {
            throw new Error();
        }

        $entityType = $this->evaluate($item->value[0]);

        if (count($item->value) < 3) {
            $filter = null;
            if (count($item->value) == 2) {
                $filter = $this->evaluate($item->value[1]);
            }

            $selectManager = $this->getInjection('selectManagerFactory')->create($entityType);
            $selectParams = $selectManager->getEmptySelectParams();
            if ($filter) {
                $selectManager->applyFilter($filter, $selectParams);
            }

            return $this->getInjection('entityManager')->getRepository($entityType)->count($selectParams);
        }

        $whereClause = [];

        $i = 1;
        while ($i < count($item->value) - 1) {
            $key = $this->evaluate($item->value[$i]);
            $value = $this->evaluate($item->value[$i + 1]);
            $whereClause[$key] = $value;
            $i = $i + 2;
        }

        return $this->getInjection('entityManager')->getRepository($entityType)->where($whereClause)->count();
    }
}
