<?php

namespace Espo\Custom\Controllers;

use Espo\Core\Exceptions\NotFound;

class Track extends \Espo\Core\Templates\Controllers\Base
{
    public function actionStream($params, $data, $request)
    {
        $id = $params['id'];
        $entity = $this->getRecordService()->read($id);

        if (!$entity) {
            throw new NotFound();
        }

        return $entity->getValueMap();
    }
}
