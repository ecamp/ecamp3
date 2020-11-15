<?php

use eCampApi\ConfigFactory;

$entity = 'MaterialItem';
$config = ConfigFactory::createConfig($entity);

array_push(
    $config['api-tools-rest']['eCampApi\\V1\\Rest\\MaterialItem\\Controller']['collection_query_whitelist'],
    'campId',
    'materialListId',
    'activityContentId'
);

$config['api-tools-content-validation'] = [
    'eCampApi\\V1\\Rest\\MaterialItem\\Controller' => [
        'input_filter' => 'eCampApi\\V1\\Rest\\MaterialItem\\Validator',
    ],
];

$config['input_filter_specs'] = [
    'eCampApi\\V1\\Rest\\MaterialItem\\Validator' => [
        0 => [
            'name' => 'article',
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
        1 => [
            'name' => 'quantity',
            'required' => false,
            'validators' => [
            ],
        ],
        2 => [
            'name' => 'unit',
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
        3 => [
            'name' => 'materialListId',
            'required' => false,
            'validators' => [
            ],
        ],
        4 => [
            'name' => 'periodId',
            'required' => false,
            'validators' => [
            ],
        ],
        5 => [
            'name' => 'activityContentId',
            'required' => false,
            'validators' => [
            ],
        ],
    ],
];

return $config;
