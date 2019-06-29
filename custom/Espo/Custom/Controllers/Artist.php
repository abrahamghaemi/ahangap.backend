<?php

namespace Espo\Custom\Controllers;

use Espo\Core\Templates\Controllers\Base as Controller;

class Artist extends Controller
{
    public function actionPopular($params, $data, $request)
    {
        $params = [];
        $this->fetchListParamsFromRequest($params, $request, $data);
        $params['orderBy'] = "like";
        $params['order'] = "desc";

        return $this->getListOfTrack($params);
    }

    public function getListOfTrack($params)
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
