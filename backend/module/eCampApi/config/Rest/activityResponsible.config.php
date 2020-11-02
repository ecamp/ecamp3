<?php

use eCampApi\ConfigFactory;

$entity = 'ActivityResponsible';
$config = ConfigFactory::createConfig($entity);

// no changes possible (only create/delete/get)
$config['api-tools-rest']["eCampApi\\V1\\Rest\\{$entity}\\Controller"]['entity_http_methods'] = ['GET', 'DELETE'];

array_push(
    $config['api-tools-rest']['eCampApi\\V1\\Rest\\ActivityResponsible\\Controller']['collection_query_whitelist'],
    'activityId',
    'campCollaborationId'
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
