<?php


namespace Espo\Services;

use \Espo\ORM\Entity;

class LastViewed extends \Espo\Core\Services\Base
{
    protected function init()
    {
        parent::init();
        $this->addDependency('serviceFactory');
        $this->addDependency('metadata');
    }

    public function getList($params)
    {
        $repository = $this->getEntityManager()->getRepository('ActionHistoryRecord');

        $actionHistoryRecordService = $this->getInjection('serviceFactory')->create('ActionHistoryRecord');

        $scopes = $this->getInjection('metadata')->get('scopes');

        $targetTypeList = array_filter(array_keys($scopes), function ($item) use ($scopes) {
            return !empty($scopes[$item]['object']);
        });

        $offset = $params['offset'];
        $maxSize = $params['maxSize'];

        $selectParams = [
            'whereClause' => [
                'userId' => $this->getUser()->id,
                'action' => 'read',
                'targetType' => $targetTypeList
            ],
            'orderBy' => [[4, true]],
            'select' => ['targetId', 'targetType', 'MAX:number', ['MAX:createdAt', 'createdAt']],
            'groupBy' => ['targetId', 'targetType']
        ];

        $collection = $repository->limit($offset, $params['maxSize'] + 1)->find($selectParams);

        foreach ($collection as $i => $entity) {
            $actionHistoryRecordService->loadParentNameFields($entity);
            $entity->set('id', \Espo\Core\Utils\Util::generateId());
        }

        if ($maxSize && count($collection) > $maxSize) {
            $total = -1;
            unset($collection[count($collection) - 1]);
        } else {
            $total = -2;
        }

        return (object) [
            'total' => $total,
            'collection' => $collection
        ];
    }
}

