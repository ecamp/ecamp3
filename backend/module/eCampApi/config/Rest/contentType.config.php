<?php

use eCampApi\ConfigFactory;

$config = ConfigFactory::createConfig('ContentType');

$config['api-tools-content-validation'] = [
    'eCampApi\\V1\\Rest\\ContentType\\Controller' => [
        'input_filter' => 'eCampApi\\V1\\Rest\\ContentType\\Validator',
    ],
];

$config['input_filter_specs'] = [
    'eCampApi\\V1\\Rest\\ContentType\\Validator' => [
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
            'name' => 'active',
            'required' => true,
            'filters' => [],
            'validators' => [],
        ],
        2 => [
            'name' => 'strategyClass',
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
                        'max' => 128,
                    ],
                ],
            ],
        ],
    ],
];

return $config;
