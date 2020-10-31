<?php

use eCampApi\ConfigFactory;

$config = ConfigFactory::createConfig('Period');

array_push(
    $config['api-tools-rest']['eCampApi\\V1\\Rest\\Period\\Controller']['collection_query_whitelist'],
    'campId'
);

$config['api-tools-content-validation'] = [
    'eCampApi\\V1\\Rest\\Period\\Controller' => [
        'input_filter' => 'eCampApi\\V1\\Rest\\Period\\Validator',
    ],
];

$config['input_filter_specs'] = [
    'eCampApi\\V1\\Rest\\Period\\Validator' => [
        0 => [
            'name' => 'start',
            'required' => true,
            'filters' => [],
            'validators' => [],
        ],
        1 => [
            'name' => 'end',
            'required' => true,
            'filters' => [],
            'validators' => [],
        ],
        2 => [
            'name' => 'description',
            'required' => false,
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
                        'max' => 128,
                    ],
                ],
            ],
        ],
    ],
];

return $config;
