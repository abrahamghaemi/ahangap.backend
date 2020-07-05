<?php


namespace Espo\Core\Utils\Database\Orm\Relations;

class HasMany extends Base
{
    protected function load($linkName, $entityName)
    {
        $linkParams = $this->getLinkParams();
        $foreignLinkName = $this->getForeignLinkName();
        $foreignEntityName = $this->getForeignEntityName();

        $relationType = isset($linkParams['relationName']) ? 'manyMany' : 'hasMany';

        $isStub = !$this->getMetadata()->get(['entityDefs', $entityName, 'fields', $linkName]);

        $relation = [
            $entityName => [
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
                        'type' => $relationType,
                        'entity' => $foreignEntityName,
                        'foreignKey' => lcfirst($foreignLinkName.'Id'),
                        'foreign' => $foreignLinkName
                    ],
                ],
            ],
        ];

        return $relation;
    }
}
