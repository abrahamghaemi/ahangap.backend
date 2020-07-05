<?php


namespace Espo\Core\Utils\Database\Orm\Relations;

use Espo\Core\Utils\Util;

class ManyMany extends Base
{
    protected function load($linkName, $entityType)
    {
        $foreignEntityName = $this->getForeignEntityName();
        $foreignLinkName = $this->getForeignLinkName();

        $linkParams = $this->getLinkParams();

        if (!empty($linkParams['relationName'])) {
            $relationName = $linkParams['relationName'];
        } else {
            $relationName = $this->getJoinTable($entityType, $foreignEntityName);
        }

        $isStub = !$this->getMetadata()->get(['entityDefs', $entityType, 'fields', $linkName]);

        $key1 = lcfirst($entityType) . 'Id';
        $key2 = lcfirst($foreignEntityName) . 'Id';

        if ($key1 === $key2) {
            if (strcmp($linkName, $foreignLinkName)) {
                $key1 = 'leftId';
                $key2 = 'rightId';
            } else {
                $key1 = 'rightId';
                $key2 = 'leftId';
            }
        }

        return [
            $entityType => [
                'fields' => [
                    $linkName.'Ids' => [
                        'type' => 'jsonArray',
                        'notStorable' => true,
                        'isLinkStub' => $isStub,
                    ],
                    $linkName.'Names' => [
                        'type' => 'jsonObject',
                        'notStorable' => true,
                        'isLinkStub' => $isStub,
                    ],
                ],
                'relations' => [
                    $linkName => [
                        'type' => 'manyMany',
                        'entity' => $foreignEntityName,
                        'relationName' => $relationName,
                        'key' => 'id',
                        'foreignKey' => 'id',
                        'midKeys' => [
                            $key1,
                            $key2,
                        ],
                        'foreign' => $foreignLinkName,
                    ],
                ],
            ],
        ];
    }

    protected function getJoinTable($tableName1, $tableName2)
    {
        $tables = $this->getSortEntities($tableName1, $tableName2);

        return Util::toCamelCase( implode('_', $tables) );
    }

    protected function getSortEntities($entity1, $entity2)
    {
        $entities = array(
            Util::toCamelCase(lcfirst($entity1)),
            Util::toCamelCase(lcfirst($entity2)),
        );

        sort($entities);

        return $entities;
    }

}
