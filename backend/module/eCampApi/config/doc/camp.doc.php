<?php

return [
    'entity' => [
        'GET' => [
            'description' => 'Returns one Camp',
            'response' => '[CAMP]',
        ],
        'PATCH' => [
            'description' => 'Update some values
- title
- motto',
            'request' => '{
                "title": "title",
                "motto": "motto"
            }',
            'response' => '[CAMP]',
        ],
        'PUT' => [
            'description' => 'Update all values
- title
- motto
',
            'request' => '{
                "title": "title",
                "motto": "motto"
            }',
            'response' => '[CAMP]',
        ],
        'DELETE' => [
            'description' => 'Delete one Camp',
        ],
    ],
    'collection' => [
        'GET' => [
            'description' => 'Lists all Camps',
        ],
        'POST' => [
            'description' => 'Creates a new Camp. 
                If you additionally provide period-details, these 
                periods will be created as well.
            ',
            'request' => '{
                "name": "name",
                "title": "title",
                "motto": "motto",
                "campTypeId": "[ID of CampType]",
                "periods": [
                    {
                        "start": "[date of start]",
                        "end": "[date of end]",
                        "description": "description"
                    }
                ]
            }',
            'response' => '',
        ],
    ],
];
