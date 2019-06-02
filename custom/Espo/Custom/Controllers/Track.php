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

        header('Content-type: audio/aac');
        header('Content-length: ' . filesize($file));
        header('Content-Disposition: filename="' . $entity->get('name'));
        header('X-Pad: avoid browser bug');
        header('Cache-Control: no-cache');
        readfile($file);
    }
}
