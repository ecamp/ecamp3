<?php

use eCampApi\ConfigFactory;

$entity = 'ActivityTypeContentType';
$config = ConfigFactory::createConfig($entity);

// read-only endpoint
$config['api-tools-rest']["eCampApi\\V1\\Rest\\{$entity}\\Controller"]['entity_http_methods'] = ['GET'];
$config['api-tools-rest']["eCampApi\\V1\\Rest\\{$entity}\\Controller"]['collection_http_methods'] = ['GET'];

array_push(
    $config['api-tools-rest']['eCampApi\\V1\\Rest\\ActivityTypeContentType\\Controller']['collection_query_whitelist'],
    'activityTypeId'
);

$config['api-tools-content-validation'] = [
    'eCampApi\\V1\\Rest\\ActivityTypeContentType\\Controller' => [
        'input_filter' => 'eCampApi\\V1\\Rest\\ActivityTypeContentType\\Validator',
    ],
];

$config['input_filter_specs'] = [
    'eCampApi\\V1\\Rest\\ActivityTypeContentType\\Validator' => [
        0 => [
            'name' => 'minNumberContentTypeInstances',
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
            'name' => 'maxNumberContentTypeInstances',
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
            'name' => 'jsonConfig',
            'required' => false,
            'filters' => [
                0 => [
                    'name' => 'Laminas\\Filter\\StringTrim',
                ],
                1 => [
                    'name' => 'Laminas\\Filter\\StripTags',
                ],
            ],
            'validators' => [],
        ],
    ],
];

return $config;
