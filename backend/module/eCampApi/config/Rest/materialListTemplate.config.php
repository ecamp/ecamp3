<?php

use eCampApi\ConfigFactory;

$entity = 'MaterialListTemplate';
$config = ConfigFactory::createConfig($entity);

// read-only endpoint
$config['api-tools-rest']["eCampApi\\V1\\Rest\\{$entity}\\Controller"]['entity_http_methods'] = ['GET'];
$config['api-tools-rest']["eCampApi\\V1\\Rest\\{$entity}\\Controller"]['collection_http_methods'] = ['GET'];

array_push(
    $config['api-tools-rest']['eCampApi\\V1\\Rest\\MaterialListTemplate\\Controller']['collection_query_whitelist'],
    'campTemplateId'
);

$config['api-tools-content-validation'] = [
    'eCampApi\\V1\\Rest\\MaterialListTemplate\\Controller' => [
        'input_filter' => 'eCampApi\\V1\\Rest\\MaterialListTemplate\\Validator',
    ],
];

$config['input_filter_specs'] = [
    'eCampApi\\V1\\Rest\\MaterialListTemplate\\Validator' => [
        0 => [
            'name' => 'name',
            'required' => true,
            'filters' => [
                0 => [
                    'name' => 'Laminas\\Filter\\StringTrim',
                ],
                1 => [
                    'name' => 'Laminas\\Filter\\StripTags',
                ],
            ],
            'validators' => [
                0 => [
                    'name' => 'Laminas\\Validator\\StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 32,
                    ],
                ],
            ],
        ],
    ],
];

return $config;
