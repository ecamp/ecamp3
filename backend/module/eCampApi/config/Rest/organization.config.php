<?php

use eCampApi\ConfigFactory;

$config = ConfigFactory::createConfig('Organization');

$config['api-tools-content-validation'] = [
    'eCampApi\\V1\\Rest\\Organization\\Controller' => [
        'input_filter' => 'eCampApi\\V1\\Rest\\Organization\\Validator',
    ],
];

$config['input_filter_specs'] = [
    'eCampApi\\V1\\Rest\\Organization\\Validator' => [
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
    ],
];

return $config;
