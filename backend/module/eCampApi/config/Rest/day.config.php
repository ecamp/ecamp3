<?php

use eCampApi\ConfigFactory;

$config = ConfigFactory::createConfig('Day');

array_push(
    $config['api-tools-rest']['eCampApi\\V1\\Rest\\Day\\Controller']['collection_query_whitelist'],
    'campId',
    'periodId'
);

$config['api-tools-content-validation'] = [
    'eCampApi\\V1\\Rest\\Day\\Controller' => [
        'input_filter' => 'eCampApi\\V1\\Rest\\Day\\Validator',
    ],
];

$config['input_filter_specs'] = [
    'eCampApi\\V1\\Rest\\Day\\Validator' => [
        0 => [
            'name' => 'dayOffset',
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
    ],
];

return $config;
