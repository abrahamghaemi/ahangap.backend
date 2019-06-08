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
}
