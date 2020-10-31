<?php

use eCampApi\ConfigFactory;

$config = ConfigFactory::createConfig('ActivityContent');

array_push(
    $config['api-tools-rest']['eCampApi\\V1\\Rest\\ActivityContent\\Controller']['collection_query_whitelist'],
    'activityId'
);

$config['api-tools-content-validation'] = [
    'eCampApi\\V1\\Rest\\ActivityContent\\Controller' => [
        'input_filter' => 'eCampApi\\V1\\Rest\\ActivityContent\\Validator',
    ],
];

$config['input_filter_specs'] = [
    'eCampApi\\V1\\Rest\\ActivityContent\\Validator' => [
        0 => [
            'name' => 'instanceName',
            'required' => false,
            'filters' => [
                0 => [
                    'name' => 'Laminas\\Filter\\StringTrim',
                ],
                1 => [
                    'name' => 'Laminas\\Filter\\StripTags',
                ],
            ],
        ],
    ],
];

return $config;
