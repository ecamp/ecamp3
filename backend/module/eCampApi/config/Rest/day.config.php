<?php

use eCampApi\ConfigFactory;

$entity = 'Day';
$config = ConfigFactory::createConfig($entity);

// disallow removal and creation of days directly via endpoint (should only be possible via period endpoint)
$config['api-tools-rest']["eCampApi\\V1\\Rest\\{$entity}\\Controller"]['entity_http_methods'] = ['GET', 'PATCH', 'PUT'];
$config['api-tools-rest']["eCampApi\\V1\\Rest\\{$entity}\\Controller"]['collection_http_methods'] = ['GET'];

array_push(
    $config['api-tools-rest']['eCampApi\\V1\\Rest\\Day\\Controller']['collection_query_whitelist'],
    'campId',
    'periodId'
);

$config['api-tools-content-validation'] = [
    'eCampApi\\V1\\Rest\\Day\\Controller' => [
        'input_filter' => 'eCampApi\\V1\\Rest\\Day\\Validator',
    ],
];

$config['input_filter_specs'] = [
    'eCampApi\\V1\\Rest\\Day\\Validator' => [
        // empty = no values chan be changed so far
    ],
];

return $config;
