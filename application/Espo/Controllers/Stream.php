<?php


namespace Espo\Controllers;

use \Espo\Core\Exceptions\Error;

class Stream extends \Espo\Core\Controllers\Base
{
    const MAX_SIZE_LIMIT = 200;

    public static $defaultAction = 'list';

    public function actionList($params, $data, $request)
    {
        $scope = $params['scope'];
        $id = isset($params['id']) ? $params['id'] : null;

        $offset = intval($request->get('offset'));
        $maxSize = intval($request->get('maxSize'));
        $after = $request->get('after');
        $filter = $request->get('filter');

        $service = $this->getService('Stream');

        $maxSizeLimit = $this->getConfig()->get('recordListMaxSizeLimit', self::MAX_SIZE_LIMIT);
        if (empty($maxSize)) {
            $maxSize = $maxSizeLimit;
        }
        if (!empty($maxSize) && $maxSize > $maxSizeLimit) {
            throw new Forbidden("Max size should should not exceed " . $maxSizeLimit . ". Use offset and limit.");
        }

        $result = $service->find($scope, $id, [
            'offset' => $offset,
            'maxSize' => $maxSize,
            'after' => $after,
            'filter' => $filter
        ]);

        return (object) [
            'total' => $result->total,
            'list' => $result->collection->getValueMapList()
        ];
    }

    public function getActionListPosts($params, $data, $request)
    {
        $scope = $params['scope'];
        $id = isset($params['id']) ? $params['id'] : null;

        $offset = intval($request->get('offset'));
        $maxSize = intval($request->get('maxSize'));
        $after = $request->get('after');

        $where = $request->get('where');

        $service = $this->getService('Stream');

        $maxSizeLimit = $this->getConfig()->get('recordListMaxSizeLimit', self::MAX_SIZE_LIMIT);
        if (empty($maxSize)) {
            $maxSize = $maxSizeLimit;
        }
        if (!empty($maxSize) && $maxSize > $maxSizeLimit) {
            throw new Forbidden("Max size should should not exceed " . $maxSizeLimit . ". Use offset and limit.");
        }

        $result = $service->find($scope, $id, [
            'offset' => $offset,
            'maxSize' => $maxSize,
            'after' => $after,
            'filter' => 'posts',
            'where' => $where
        ]);

        return (object) [
            'total' => $result->total,
            'list' => $result->collection->getValueMapList()
        ];
    }
}
