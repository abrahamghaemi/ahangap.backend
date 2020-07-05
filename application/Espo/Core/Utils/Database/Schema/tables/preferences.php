<?php


return [

    'unset' => [
        '__APPEND__',
        'Preferences',
    ],
    'unsetIgnore' => [
        '__APPEND__',
        ['Preferences', 'fields', 'id'],
        ['Preferences', 'fields', 'data'],
    ],
    'Preferences' => [
        'fields' => [
            'id' => [
                'dbType' => 'varchar',
                'len' => 24,
                'type' => 'id',
            ],
            'data' => [
                'type' => 'text',
                'len' => 16777216,
            ]
        ]
    ],
];
