<?php

namespace Espo\Custom\Controllers;

use Espo\Core\Templates\Controllers\Base as Controller;

class Artist extends Controller
{
    public function actionSearch($params, $data, $request)
    {
        $params = [];
        $this->fetchListParamsFromRequest($params, $request, $data);

        return $this->getListOfTrack($params);
    }


    public function actionPopular($params, $data, $request)
    {
        $params = [];
        $this->fetchListParamsFromRequest($params, $request, $data);
        $params['orderBy'] = "like";
        $params['order'] = "desc";

        return $this->getListOfTrack($params);
    }

    public function actionNewest($params, $data, $request)
    {
        $params = [];
        $this->fetchListParamsFromRequest($params, $request, $data);
        $where = [[
            "type" => "lastXDays",
            "attribute" => "createdAt",
            "value" => 30
        ]];
        $params['where'] = $where;

        return $this->getListOfTrack($params);
    }

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

        $result = $this->getRecordService()->find($params);

        if($_SERVER['HTTP_CLIENTID']) {
            $collection = [];
            foreach ($result['collection']->getValueMapList() as $item) {
                $item = (array)$item;
                $item['liked'] = $this->hasLiked($item['id'], $_SERVER['HTTP_CLIENTID']);
                array_push($collection, (object)$item);
            }
            return array(
                'total' => $result['total'],
                'list' => $collection
            );
        }

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
    public function hasLiked($artist_id, $client_id)
    {
        $pdo = $this->getEntityManager()->getPDO();
        $sql = "select * from user_artist where deleted = 0 and user_id = " . $pdo->quote($client_id) . " and artist_id = " . $pdo->quote($artist_id);
        $sth = $pdo->prepare($sql);
        $sth->execute();

        return $sth->fetchColumn() ? true : false;
    }
}
