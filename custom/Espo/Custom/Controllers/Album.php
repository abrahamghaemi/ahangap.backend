<?php

namespace Espo\Custom\Controllers;

class Album extends \Espo\Core\Templates\Controllers\Base
{
    public function actionPopular($params, $data, $request)
    {
        $params = [];
        $this->fetchListParamsFromRequest($params, $request, $data);

        return $this->getListOfTrack($params);
    }

    public function actionNewest($params, $data, $request)
    {
        $params = [];
        $this->fetchListParamsFromRequest($params, $request, $data);
        // where[0][type]=lastXDays&where[0][attribute]=publishedDate&where[0][value]=30
        $where = [[
            "type" => "lastXDays",
            "attribute" => "published",
            "value" => 420
        ]];
        $params['where'] = $where;

        return $this->getListOfTrack($params);
    }

    private function getListOfTrack($params)
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
