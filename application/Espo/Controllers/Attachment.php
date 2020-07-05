<?php


namespace Espo\Controllers;

use \Espo\Core\Exceptions\Forbidden;
use \Espo\Core\Exceptions\BadRequest;

class Attachment extends \Espo\Core\Controllers\Record
{
    public function actionList($params, $data, $request)
    {
        if (!$this->getUser()->isAdmin()) {
            throw new Forbidden();
        }
        return parent::actionList($params, $data, $request);
    }

    public function postActionGetAttachmentFromImageUrl($params, $data)
    {
        if (empty($data->url)) throw new BadRequest();
        if (empty($data->field)) throw new BadRequest('postActionGetAttachmentFromImageUrl: No field specified');

        return $this->getRecordService()->getAttachmentFromImageUrl($data)->getValueMap();
    }

    public function postActionGetCopiedAttachment($params, $data)
    {
        if (empty($data->id)) throw new BadRequest();
        if (empty($data->field)) throw new BadRequest('postActionGetCopiedAttachment copy: No field specified');

        return $this->getRecordService()->getCopiedAttachment($data)->getValueMap();
    }
}
