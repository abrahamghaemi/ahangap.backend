<?php


namespace Espo\Hooks\Integration;

use Espo\ORM\Entity;

class GoogleMaps extends \Espo\Core\Hooks\Base
{
    public function afterSave(Entity $entity)
    {
        if ($entity->id === 'GoogleMaps') {
            if (!$entity->get('enabled') || !$entity->get('apiKey')) {
                $this->getConfig()->set('googleMapsApiKey', null);
                $this->getConfig()->save();
                return;
            }
            $this->getConfig()->set('googleMapsApiKey', $entity->get('apiKey'));
            $this->getConfig()->save();
        }
    }
}
