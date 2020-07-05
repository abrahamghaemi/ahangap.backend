<?php


namespace Espo\Entities;

class Attachment extends \Espo\Core\ORM\Entity
{
    public function getSourceId()
    {
        $sourceId = $this->get('sourceId');
        if (!$sourceId) {
            $sourceId = $this->id;
        }
        return $sourceId;
    }

}
