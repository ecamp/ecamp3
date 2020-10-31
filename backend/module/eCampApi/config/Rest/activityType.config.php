<?php

use eCampApi\ConfigFactory;

$config = ConfigFactory::createConfig('ActivityType');

array_push(
    $config['api-tools-rest']['eCampApi\\V1\\Rest\\ActivityType\\Controller']['collection_query_whitelist'],
    'campTypeId'
);

$config['api-tools-content-validation'] = [
    'eCampApi\\V1\\Rest\\ActivityType\\Controller' => [
        'input_filter' => 'eCampApi\\V1\\Rest\\ActivityType\\Validator',
    ],
];

$config['input_filter_specs'] = [
    'eCampApi\\V1\\Rest\\ActivityType\\Validator' => [
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
            'name' => 'defaultColor',
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
            'name' => 'defaultNumberingStyle',
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
