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
        // empty = no values chan be changed so far
    ],
];

return $config;
