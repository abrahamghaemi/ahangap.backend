<?php


namespace Espo\Core\Utils\Database\Orm\Fields;

use Espo\Core\Utils\Util;

class Currency extends Base
{
    protected function load($fieldName, $entityType)
    {
        $converedFieldName = $fieldName . 'Converted';

        $currencyColumnName = Util::toUnderScore($fieldName);

        $alias = $fieldName . 'CurrencyRate';

        $defs = [
            $entityType => [
                'fields' => [
                    $fieldName => [
                        'type' => 'float',
                    ]
                ]
            ]
        ];

        $part = Util::toUnderScore($entityType) . "." . $currencyColumnName;
        $leftJoins = [
            [
                'Currency',
                $alias,
                [$alias . '.id:' => $fieldName . 'Currency']
            ]
        ];

        $foreignAlias = "{$alias}{$entityType}Foreign";

        $params = $this->getFieldParams($fieldName);
        if (!empty($params['notStorable'])) {
            $defs[$entityType]['fields'][$fieldName]['notStorable'] = true;
        } else {
            $defs[$entityType]['fields'][$fieldName . 'Converted'] = [
                'type' => 'float',
                'select' => [
                    'sql' => $part . " * {$alias}.rate",
                    'leftJoins' => $leftJoins,
                ],
                'selectForeign' => [
                    'sql' => "{alias}.{$currencyColumnName} * {$foreignAlias}.rate",
                    'leftJoins' => [
                        [
                            'Currency',
                            $foreignAlias,
                            [
                                $foreignAlias . '.id:' => "{alias}.{$fieldName}Currency"
                            ]
                        ]
                    ],
                ],
                'where' =>
                [
                        "=" => ['sql' => $part . " * {$alias}.rate = {value}", 'leftJoins' => $leftJoins],
                        ">" => ['sql' => $part . " * {$alias}.rate > {value}", 'leftJoins' => $leftJoins],
                        "<" => ['sql' => $part . " * {$alias}.rate < {value}", 'leftJoins' => $leftJoins],
                        ">=" => ['sql' => $part . " * {$alias}.rate >= {value}", 'leftJoins' => $leftJoins],
                        "<=" => ['sql' => $part . " * {$alias}.rate <= {value}", 'leftJoins' => $leftJoins],
                        "<>" => ['sql' => $part . " * {$alias}.rate <> {value}", 'leftJoins' => $leftJoins],
                        "IS NULL" => ['sql' => $part . ' IS NULL'],
                        "IS NOT NULL" => ['sql' => $part . ' IS NOT NULL'],
                ],
                'notStorable' => true,
                'orderBy' => [
                    'sql' => $converedFieldName . " {direction}",
                    'leftJoins' => $leftJoins,
                ],
            ];

            $defs[$entityType]['fields'][$fieldName]['orderBy'] = [
                'sql' => $part . " * {$alias}.rate {direction}",
                'leftJoins' => $leftJoins,
            ];
        }

        return $defs;
    }
}
