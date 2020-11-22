<?php

use eCampApi\ConfigFactory;

$config = ConfigFactory::createConfig('CampCollaboration');

array_push(
    $config['api-tools-rest']['eCampApi\\V1\\Rest\\CampCollaboration\\Controller']['collection_query_whitelist'],
    'campId',
    'userId',
    'inviteKey'
);

$config['api-tools-content-validation'] = [
    'eCampApi\\V1\\Rest\\CampCollaboration\\Controller' => [
        'input_filter' => 'eCampApi\\V1\\Rest\\CampCollaboration\\Validator',
    ],
];

$config['input_filter_specs'] = [
    'eCampApi\\V1\\Rest\\CampCollaboration\\Validator' => [
        0 => [
            'name' => 'status',
            'required' => false,
            'filters' => [
                0 => [
                    'name' => 'Laminas\\Filter\\StringTrim',
                ],
                1 => [
                    'name' => 'Laminas\\Filter\\StripTags',
                ],
            ],
            'validators' => [],
        ],
        1 => [
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
            'validators' => [],
        ],
        2 => [
            'name' => 'collaborationAcceptedBy',
            'required' => false,
            'filters' => [
                0 => [
                    'name' => 'Laminas\\Filter\\StringTrim',
                ],
                1 => [
                    'name' => 'Laminas\\Filter\\StripTags',
                ],
            ],
            'validators' => [],
        ],
        3 => [
            'name' => 'inviteKey',
            'required' => false,
            'filters' => [
                0 => [
                    'name' => 'Laminas\\Filter\\StringTrim',
                ],
                1 => [
                    'name' => 'Laminas\\Filter\\StripTags',
                ],
            ],
            'validators' => [],
        ],
    ],
];

return $config;
