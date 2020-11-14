<?php

use eCampApi\ConfigFactory;

$entity = 'MaterialList';
$config = ConfigFactory::createConfig($entity);

array_push(
    $config['api-tools-rest']['eCampApi\\V1\\Rest\\MaterialList\\Controller']['collection_query_whitelist'],
    'campId'
);

$config['api-tools-content-validation'] = [
    'eCampApi\\V1\\Rest\\MaterialList\\Controller' => [
        'input_filter' => 'eCampApi\\V1\\Rest\\MaterialList\\Validator',
    ],
];

$config['input_filter_specs'] = [
    'eCampApi\\V1\\Rest\\MaterialList\\Validator' => [
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
