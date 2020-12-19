<?php

use eCampApi\ConfigFactory;

$config = ConfigFactory::createConfig('Activity', 'Activities');

array_push(
    $config['api-tools-rest']['eCampApi\\V1\\Rest\\Activity\\Controller']['collection_query_whitelist'],
    'campId',
    'periodId'
);

$config['api-tools-content-validation'] = [
    'eCampApi\\V1\\Rest\\Activity\\Controller' => [
        'input_filter' => 'eCampApi\\V1\\Rest\\Activity\\Validator',
    ],
];

$config['input_filter_specs'] = [
    'eCampApi\\V1\\Rest\\Activity\\Validator' => [
        0 => [
            'name' => 'title',
            'required' => true,
            'filters' => [],
            'validators' => [],
        ],
        1 => [
            'name' => 'location',
            'required' => false,
            'filters' => [],
            'validators' => [],
        ],
        2 => [
            'name' => 'progress',
            'required' => false,
            'filters' => [],
            'validators' => [],
        ],
        3 => [
            'name' => 'campCollaborations',
            'required' => false,
            'filters' => [],
            'validators' => [],
        ],
        4 => [
            'name' => 'scheduleEntries',
            'required' => false,
            'filters' => [],
            'validators' => [],
        ],
        5 => [
            'name' => 'activityCategoryId',
            'required' => false,
            'filters' => [],
            'validators' => [],
        ],
    ],
];

return $config;
