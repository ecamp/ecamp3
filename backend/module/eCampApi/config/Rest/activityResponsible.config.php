<?php

use eCampApi\ConfigFactory;

$config = ConfigFactory::createConfig('ActivityResponsible');

array_push(
    $config['api-tools-rest']['eCampApi\\V1\\Rest\\ActivityResponsible\\Controller']['collection_query_whitelist'],
    'campId'
);

$config['api-tools-content-validation'] = [
    'eCampApi\\V1\\Rest\\ActivityResponsible\\Controller' => [
        'input_filter' => 'eCampApi\\V1\\Rest\\ActivityResponsible\\Validator',
    ],
];

$config['input_filter_specs'] = [
    'eCampApi\\V1\\Rest\\ActivityResponsible\\Validator' => [
    ],
];

return $config;
