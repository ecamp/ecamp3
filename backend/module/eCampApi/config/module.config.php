<?php

return [
    'router' => [
        'routes' => [
            'e-camp-api.rpc.index' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/[:action]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rpc\\Index\\IndexController',
                        'action' => 'index',
                    ],
                ],
            ],
            'e-camp-api.rpc.auth' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/auth[/:action]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rpc\\Auth\\AuthController',
                        'action' => 'index',
                    ],
                ],
            ],
            'e-camp-api.rpc.auth.google' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/auth/google',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rpc\\Auth\\AuthController',
                        'action' => 'google',
                    ],
                ],
            ],
            'e-camp-api.rpc.auth.pbsmidata' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/auth/pbsmidata',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rpc\\Auth\\AuthController',
                        'action' => 'pbsmidata',
                    ],
                ],
            ],
            'e-camp-api.rpc.invitation' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/invitation[/:action][/:inviteKey]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rpc\\Invitation\\InvitationController',
                        'action' => 'index',
                    ],
                ],
            ],
            'e-camp-api.rpc.register' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/register[/:action]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rpc\\Register\\RegisterController',
                        'action' => 'register',
                    ],
                ],
            ],
            'e-camp-api.rpc.profile' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/profile',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rpc\\Profile\\ProfileController',
                        'action' => 'index',
                    ],
                ],
            ],
            'e-camp-api.rpc.printer' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/printer',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rpc\\Printer\\PrinterController',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            'eCampApi\\V1\\Rpc\\Auth\\AuthController' => \Laminas\Di\Container\ServiceManager\AutowireFactory::class,
            'eCampApi\\V1\\Rpc\\Index\\IndexController' => \Laminas\Di\Container\ServiceManager\AutowireFactory::class,
            'eCampApi\\V1\\Rpc\\Invitation\\InvitationController' => \Laminas\Di\Container\ServiceManager\AutowireFactory::class,
            'eCampApi\\V1\\Rpc\\Register\\RegisterController' => \Laminas\Di\Container\ServiceManager\AutowireFactory::class,
            'eCampApi\\V1\\Rpc\\Profile\\ProfileController' => \Laminas\Di\Container\ServiceManager\AutowireFactory::class,
            'eCampApi\\V1\\Rpc\\Printer\\PrinterController' => \Laminas\Di\Container\ServiceManager\AutowireFactory::class,
        ],
    ],
    'api-tools-content-negotiation' => [
        'controllers' => [
            'eCampApi\\V1\\Rpc\\Index\\IndexController' => 'HalJson',
            'eCampApi\\V1\\Rpc\\Invitation\\InvitationController' => 'HalJson',
            'eCampApi\\V1\\Rpc\\Auth\\AuthController' => 'HalJson',
            'eCampApi\\V1\\Rpc\\Register\\RegisterController' => 'HalJson',
            'eCampApi\\V1\\Rpc\\Profile\\ProfileController' => 'HalJson',
            'eCampApi\\V1\\Rpc\\Printer\\PrinterController' => 'HalJson',
        ],
        'accept_whitelist' => [
            'eCampApi\\V1\\Rpc\\Index\\IndexController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ],
            'eCampApi\\V1\\Rpc\\Index\\InvitationController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ],
            'eCampApi\\V1\\Rpc\\Auth\\AuthController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ],
            'eCampApi\\V1\\Rpc\\Register\\RegisterController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ],
            'eCampApi\\V1\\Rpc\\Profile\\ProfileController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ],
            'eCampApi\\V1\\Rpc\\Printer\\PrinterController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ],
        ],
        'content_type_whitelist' => [
            'eCampApi\\V1\\Rpc\\Index\\IndexController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rpc\\Index\\InvitationController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rpc\\Auth\\AuthController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rpc\\Register\\RegisterController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rpc\\Profile\\ProfileController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rpc\\Printer\\PrinterController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
        ],
    ],
    'api-tools-hal' => [
        'metadata_map' => [
            \eCamp\Lib\Entity\EntityLink::class => [
                'route_identifier_name' => 'id',
                'entity_identifier_name' => 'id',
                'route_name' => '',
            ],
            \eCamp\Lib\Entity\EntityLinkCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => '',
                'is_collection' => true,
            ],
        ],
    ],
    'api-tools-rpc' => [
        'eCampApi\\V1\\Rpc\\Auth\\AuthController' => [
            'service_name' => 'Auth',
            'http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'route_name' => 'e-camp-api.rpc.auth',
            'collection_query_whitelist' => [
                'callback',
            ],
        ],
        'eCampApi\\V1\\Rpc\\Index\\IndexController' => [
            'service_name' => 'Index',
            'http_methods' => [
                0 => 'GET',
            ],
            'route_name' => 'e-camp-api.rpc.index',
        ],
        'eCampApi\\V1\\Rpc\\Index\\InvitationController' => [
            'service_name' => 'Invitation',
            'http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'route_name' => 'e-camp-api.rpc.invitation',
        ],
        'eCampApi\\V1\\Rpc\\Register\\RegisterController' => [
            'service_name' => 'Register',
            'http_methods' => [
                0 => 'POST',
            ],
            'route_name' => 'e-camp-api.rpc.register',
        ],
        'eCampApi\\V1\\Rpc\\Profile\\ProfileController' => [
            'service_name' => 'Profile',
            'http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
            ],
            'route_name' => 'e-camp-api.rpc.profile',
        ],
        'eCampApi\\V1\\Rpc\\Printer\\PrinterController' => [
            'service_name' => 'Printer',
            'http_methods' => [
                0 => 'POST',
            ],
            'route_name' => 'e-camp-api.rpc.printer',
        ],
    ],
];
