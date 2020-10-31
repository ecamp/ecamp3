<?php

use eCampApi\ConfigFactory;

$config = ConfigFactory::createConfig('User');

array_push(
    $config['api-tools-rest']['eCampApi\\V1\\Rest\\User\\Controller']['collection_query_whitelist'],
    'search'
);

$config['api-tools-content-validation'] = [
    'eCampApi\\V1\\Rest\\User\\Controller' => [
        'input_filter' => 'eCampApi\\V1\\Rest\\User\\Validator',
    ],
];

$config['input_filter_specs'] = [
    'eCampApi\\V1\\Rest\\User\\Validator' => [
        0 => [
            'name' => 'username',
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
                        'max' => 32,
                    ],
                ],
            ],
        ],
        1 => [
            'name' => 'state',
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
                        'max' => 16,
                    ],
                ],
            ],
        ],
        2 => [
            'name' => 'role',
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
                        'max' => 16,
                    ],
                ],
            ],
        ],
    ],
];

return $config;
