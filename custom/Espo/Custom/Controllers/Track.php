<?php

namespace Espo\Custom\Controllers;

use Espo\Core\Exceptions\NotFound;

class Track extends \Espo\Core\Templates\Controllers\Base
{
    public function actionStream($params, $data, $request)
    {
        $id = $params['id'];
        $entity = $this->getRecordService()->stream($id);

        if (!$entity) {
            throw new NotFound();
        }

        $file = parse_url(
            $entity->get('stream'), PHP_URL_PATH
        );

        $file = '/home/apps/music/repository' . $file;


		header('Transfer-Encoding: chunked');
		header('Pragma: public');
		header("Content-Transfer-Encoding: binary\n");
		header('Cache-Control: public, must-revalidate, max-age=0');
        header('Content-type: audio/aac');
        header('accept-ranges: bytes');
        header('Content-length: ' . filesize($file));
        header('Content-Disposition: filename="' . $entity->get('name'));
		header('Connection: close');
		ob_clean();
		flush();

        echo readfile($file);
		die;
    }

    public function actionLike($params, $data, $request)
    {
        $id = $params['id'];
        $entity = $this->getRecordService()->read($id);
        $like = $entity->get('likes');

        $like = (int) $like + 1;
        $d['likes'] = $like;

        if ($entity = $this->getRecordService()->update($id, $d)) {
            return $entity->getValueMap();
        }

        throw new Error();
    }

    public function actionNew($params, $data, $request)
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


        $where = [[
            "type" => "lastSevenDays",
            "attribute" => "publishedDate"
        ]];
        $params['where'] = $where;

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
