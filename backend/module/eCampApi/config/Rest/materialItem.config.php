<?php

use eCampApi\ConfigFactory;

$entity = 'MaterialItem';
$config = ConfigFactory::createConfig($entity);

array_push(
    $config['api-tools-rest']['eCampApi\\V1\\Rest\\MaterialItem\\Controller']['collection_query_whitelist'],
    'campId',
    'materialListId'
);

$config['api-tools-content-validation'] = [
    'eCampApi\\V1\\Rest\\MaterialItem\\Controller' => [
        'input_filter' => 'eCampApi\\V1\\Rest\\MaterialItem\\Validator',
    ],
];

$config['input_filter_specs'] = [
    'eCampApi\\V1\\Rest\\MaterialItem\\Validator' => [
        // empty = no values chan be changed so far
    ],
];

return $config;
