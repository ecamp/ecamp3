<?php

use eCampApi\ConfigFactory;

$config = ConfigFactory::createConfig('ActivityCategory', 'ActivityCategories');

array_push(
    $config['api-tools-rest']['eCampApi\\V1\\Rest\\ActivityCategory\\Controller']['collection_query_whitelist'],
    'campId'
);

$config['api-tools-content-validation'] = [
    'eCampApi\\V1\\Rest\\ActivityCategory\\Controller' => [
        'input_filter' => 'eCampApi\\V1\\Rest\\ActivityCategory\\Validator',
    ],
];

$config['input_filter_specs'] = [
    'eCampApi\\V1\\Rest\\ActivityCategory\\Validator' => [
        0 => [
            'name' => 'short',
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
        1 => [
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
        2 => [
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
                1 => [
                    'name' => 'Laminas\\Validator\\Regex',
                    'options' => [
                        'pattern' => '/#([a-f0-9]{3}){1,2}\b/i',
                    ],
                ],
            ],
        ],
        3 => [
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
        4 => [
            'name' => 'activityTypeId',
        ],
    ],
];

return $config;
