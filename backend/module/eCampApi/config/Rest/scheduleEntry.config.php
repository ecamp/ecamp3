<?php

use eCampApi\ConfigFactory;

$config = ConfigFactory::createConfig('ScheduleEntry', 'ScheduleEntries');

array_push(
    $config['api-tools-rest']['eCampApi\\V1\\Rest\\ScheduleEntry\\Controller']['collection_query_whitelist'],
    'activityId'
);

$config['api-tools-content-validation'] = [
    'eCampApi\\V1\\Rest\\ScheduleEntry\\Controller' => [
        'input_filter' => 'eCampApi\\V1\\Rest\\ScheduleEntry\\Validator',
    ],
];

$config['input_filter_specs'] = [
    'eCampApi\\V1\\Rest\\ScheduleEntry\\Validator' => [
        0 => [
            'name' => 'start',
            'required' => true,
            'filters' => [
                0 => [
                    'name' => 'Laminas\\Filter\\StripTags',
                ],
                1 => [
                    'name' => 'Laminas\\Filter\\Digits',
                ],
            ],
            'validators' => [],
        ],
        1 => [
            'name' => 'length',
            'required' => true,
            'filters' => [
                0 => [
                    'name' => 'Laminas\\Filter\\StripTags',
                ],
                1 => [
                    'name' => 'Laminas\\Filter\\Digits',
                ],
            ],
            'validators' => [],
        ],
        2 => [
            'name' => 'left',
            'required' => false,
            'filters' => [],
            'validators' => [],
        ],
        3 => [
            'name' => 'width',
            'required' => false,
            'filters' => [],
            'validators' => [],
        ],
    ],
];

return $config;
