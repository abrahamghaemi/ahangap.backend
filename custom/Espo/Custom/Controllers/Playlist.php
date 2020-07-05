<?php

namespace Espo\Custom\Controllers;

class Playlist extends \Espo\Core\Templates\Controllers\Base
{

    public function actionList($params, $data, $request)
    {
 if (!$this->getAcl()->check($this->name, 'read')) {
            throw new Forbidden();
        }

        $params = [];
        $this->fetchListParamsFromRequest($params, $request, $data);

        $maxSizeLimit = $this->getConfig()->get('recordListMaxSizeLimit', self::MAX_SIZE_LIMIT);
        if (empty($params['maxSize'])) {
            $params['maxSize'] = $maxSizeLimit;
        }
        if (!empty($params['maxSize']) && $params['maxSize'] > $maxSizeLimit) {
            throw new Forbidden("Max size should should not exceed " . $maxSizeLimit . ". Use offset and limit.");
        }

        if($_SERVER['HTTP_CLIENTID']) {

            $where = [['type'=>'isFalse', 'attribute'=>'private']];
            $params['where'] = $where;
            $params['orderBy'] = 'createdAt';
            $params['order'] = 'desc';
        }


        $result = $this->getRecordService()->find($params);

        if (is_array($result)) {
            return [
                'total' => $result['total'],
                'list' => isset($result['collection']) ? $result['collection']->getValueMapList() : $result['list']
            ];
        }

        return [
            'total' => $result->total,
            'list' => isset($result->collection) ? $result->collection->getValueMapList() : $result->list
        ];
    }
    public function actionPublic($params, $data, $request)
    {

		return $this->getListOfPlaylist($params);

    }


	public function getListOfPlaylist($params)
    {
        if (!$this->getAcl()->check($this->name, 'read')) {
            throw new Forbidden();
        }

        $maxSizeLimit = $this->getConfig()->get('recordListMaxSizeLimit', self::MAX_SIZE_LIMIT);
        if (empty($params['maxSize'])) {
            $params['maxSize'] = $maxSizeLimit;
        }
        if (!empty($params['maxSize']) && $params['maxSize'] > $maxSizeLimit) {
            throw new Forbidden("Max size should should not exceed " . $maxSizeLimit . ". Use offset and limit.");
        }


        $result = $this->getRecordService()->find($params);

        if (is_array($result)) {
            return [
                'total' => $result['total'],
                'list' => isset($result['collection']) ? $result['collection']->getValueMapList() : $result['list']
            ];
        }

        return [
            'total' => $result->total,
            'list' => isset($result->collection) ? $result->collection->getValueMapList() : $result->list
        ];
    }
}
