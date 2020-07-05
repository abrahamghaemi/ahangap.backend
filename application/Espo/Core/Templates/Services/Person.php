<?php


namespace Espo\Core\Templates\Services;

use \Espo\ORM\Entity;

class Person extends \Espo\Services\Record
{
    protected function getDuplicateWhereClause(Entity $entity, $data)
    {
        $whereClause = [
            'OR' => []
        ];
        $toCheck = false;
        if ($entity->get('firstName') || $entity->get('lastName')) {
            $part = [];
            $part['firstName'] = $entity->get('firstName');
            $part['lastName'] = $entity->get('lastName');
            $whereClause['OR'][] = $part;
            $toCheck = true;
        }
        if (
            ($entity->get('emailAddress') || $entity->get('emailAddressData'))
            &&
            ($entity->isNew() || $entity->isAttributeChanged('emailAddress') || $entity->isAttributeChanged('emailAddressData'))
        ) {
            if ($entity->get('emailAddress')) {
                $list = [$entity->get('emailAddress')];
            }
            if ($entity->get('emailAddressData')) {
                foreach ($entity->get('emailAddressData') as $row) {
                    if (!in_array($row->emailAddress, $list)) {
                        $list[] = $row->emailAddress;
                    }
                }
            }
            foreach ($list as $emailAddress) {
                $whereClause['OR'][] = [
                    'emailAddress' => $emailAddress
                ];
                $toCheck = true;
            }
        }
        if (!$toCheck) {
            return false;
        }

        return $whereClause;
    }
}

