<?php

use eCampApi\ConfigFactory;

$entity = 'ActivityCategoryTemplate';
$config = ConfigFactory::createConfig($entity);

// read-only endpoint
$config['api-tools-rest']["eCampApi\\V1\\Rest\\{$entity}\\Controller"]['entity_http_methods'] = ['GET'];
$config['api-tools-rest']["eCampApi\\V1\\Rest\\{$entity}\\Controller"]['collection_http_methods'] = ['GET'];

array_push(
    $config['api-tools-rest']['eCampApi\\V1\\Rest\\ActivityCategoryTemplate\\Controller']['collection_query_whitelist'],
    'campTemplateId'
);

$config['api-tools-content-validation'] = [
    'eCampApi\\V1\\Rest\\ActivityCategoryTemplate\\Controller' => [
        'input_filter' => 'eCampApi\\V1\\Rest\\ActivityCategoryTemplate\\Validator',
    ],
];

$config['input_filter_specs'] = [
    'eCampApi\\V1\\Rest\\ActivityCategoryTemplate\\Validator' => [
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
                        'max' => 64,
                    ],
                ],
            ],
        ],
        1 => [
            'name' => 'color',
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
                        'max' => 8,
                    ],
                ],
            ],
        ],
        2 => [
            'name' => 'numberingStyle',
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
                        'max' => 1,
                    ],
                ],
            ],
        ],
    ],
];

return $config;
