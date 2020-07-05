<?php


namespace Espo\Controllers;

use \Espo\Core\Exceptions\Forbidden;

class LastViewed extends \Espo\Core\Controllers\Base
{
    public function getActionIndex($params, $data, $request)
    {
        $params = [];

        $params['offset'] = $request->get('offset', 0);
        $params['maxSize'] = $request->get('maxSize');

        $maxSizeLimit = $this->getConfig()->get('recordListMaxSizeLimit', \Espo\Core\Controllers\Record::MAX_SIZE_LIMIT);
        if (empty($params['maxSize'])) {
            $params['maxSize'] = $maxSizeLimit;
        }
        if (!empty($params['maxSize']) && $params['maxSize'] > $maxSizeLimit) {
            throw new Forbidden("Max size should should not exceed " . $maxSizeLimit . ". Use offset and limit.");
        }

        $result = $this->getServiceFactory()->create('LastViewed')->getList($params);

        return (object) [
            'total' => $result->total,
            'list' => $result->collection->getValueMapList()
        ];
    }
}
