<?php

return [
    'router' => [
        'routes' => [
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
            'e-camp-api.rpc.invitation.find' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/invitations[/:inviteKey]/find',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rpc\\Invitation\\InvitationController',
                        'action' => 'find',
                    ],
                ],
            ],
            'e-camp-api.rpc.invitation' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/invitations[/:inviteKey][/:action]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rpc\\Invitation\\InvitationController',
                        'action' => 'index',
                    ],
                ],
            ],
            'e-camp-api.rpc.invitation.reject' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/invitations[/:inviteKey]/reject',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rpc\\Invitation\\UpdateInvitationController',
                        'action' => 'reject',
                    ],
                ],
            ],
            'e-camp-api.rpc.invitation.accept' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/invitations[/:inviteKey]/accept',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rpc\\Invitation\\UpdateInvitationController',
                        'action' => 'accept',
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
        ],
    ],
    'controllers' => [
        'factories' => [
            'eCampApi\\V1\\Rpc\\Auth\\AuthController' => 'Laminas\\Di\\Container\\ServiceManager\\AutowireFactory',
            'eCampApi\\V1\\Rpc\\Index\\IndexController' => 'Laminas\\Di\\Container\\ServiceManager\\AutowireFactory',
            'eCampApi\\V1\\Rpc\\Invitation\\InvitationController' => 'Laminas\\Di\\Container\\ServiceManager\\AutowireFactory',
            'eCampApi\\V1\\Rpc\\Invitation\\UpdateInvitationController' => 'Laminas\\Di\\Container\\ServiceManager\\AutowireFactory',
            'eCampApi\\V1\\Rpc\\Printer\\PrinterController' => 'Laminas\\Di\\Container\\ServiceManager\\AutowireFactory',
            'eCampApi\\V1\\Rpc\\Profile\\ProfileController' => 'Laminas\\Di\\Container\\ServiceManager\\AutowireFactory',
            'eCampApi\\V1\\Rpc\\Register\\RegisterController' => 'Laminas\\Di\\Container\\ServiceManager\\AutowireFactory',
        ],
    ],
    'api-tools-content-negotiation' => [
        'controllers' => [
            'eCampApi\\V1\\Rpc\\Auth\\AuthController' => 'HalJson',
            'eCampApi\\V1\\Rpc\\Index\\IndexController' => 'HalJson',
            'eCampApi\\V1\\Rpc\\Invitation\\InvitationController' => 'HalJson',
            'eCampApi\\V1\\Rpc\\Invitation\\UpdateInvitationController' => 'HalJson',
            'eCampApi\\V1\\Rpc\\Printer\\PrinterController' => 'HalJson',
            'eCampApi\\V1\\Rpc\\Profile\\ProfileController' => 'HalJson',
            'eCampApi\\V1\\Rpc\\Register\\RegisterController' => 'HalJson',
        ],
        'accept_whitelist' => [
            'eCampApi\\V1\\Rpc\\Auth\\AuthController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
                3 => 'application/vnd.e-camp-api.v1+json',
                4 => 'application/json',
                5 => 'application/*+json',
                6 => 'application/vnd.e-camp-api.v1+json',
                7 => 'application/json',
                8 => 'application/*+json',
            ],
            'eCampApi\\V1\\Rpc\\Index\\IndexController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ],
            'eCampApi\\V1\\Rpc\\Invitation\\InvitationController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
                3 => 'application/vnd.e-camp-api.v1+json',
                4 => 'application/json',
                5 => 'application/*+json',
            ],
            'eCampApi\\V1\\Rpc\\Invitation\\UpdateInvitationController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
                3 => 'application/vnd.e-camp-api.v1+json',
                4 => 'application/json',
                5 => 'application/*+json',
            ],
            'eCampApi\\V1\\Rpc\\Printer\\PrinterController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ],
            'eCampApi\\V1\\Rpc\\Profile\\ProfileController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ],
            'eCampApi\\V1\\Rpc\\Register\\RegisterController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ],
        ],
        'content_type_whitelist' => [
            'eCampApi\\V1\\Rpc\\Auth\\AuthController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/vnd.e-camp-api.v1+json',
                3 => 'application/json',
                4 => 'application/vnd.e-camp-api.v1+json',
                5 => 'application/json',
            ],
            'eCampApi\\V1\\Rpc\\Index\\IndexController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rpc\\Invitation\\InvitationController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/vnd.e-camp-api.v1+json',
                3 => 'application/json',
            ],
            'eCampApi\\V1\\Rpc\\Invitation\\UpdateInvitationController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/vnd.e-camp-api.v1+json',
                3 => 'application/json',
            ],
            'eCampApi\\V1\\Rpc\\Printer\\PrinterController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rpc\\Profile\\ProfileController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rpc\\Register\\RegisterController' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
        ],
    ],
    'api-tools-hal' => [
        'metadata_map' => [
            'eCamp\\Lib\\Entity\\EntityLink' => [
                'route_identifier_name' => 'id',
                'entity_identifier_name' => 'id',
                'route_name' => '',
            ],
            'eCamp\\Lib\\Entity\\EntityLinkCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => '',
                'is_collection' => true,
            ],
        ],
    ],
    'api-tools-rpc' => [
        'eCampApi\\V1\\Rpc\\Auth\\AuthController' => [
            'http_methods' => [
                0 => 'GET',
                1 => 'POST',
                2 => 'GET',
                3 => 'POST',
                4 => 'GET',
                5 => 'POST',
            ],
            'route_name' => 'e-camp-api.rpc.auth',
            'collection_query_whitelist' => [
                0 => 'callback',
            ],
        ],
        'eCampApi\\V1\\Rpc\\Index\\IndexController' => [
            'http_methods' => [
                0 => 'GET',
            ],
            'route_name' => 'e-camp-api.rpc.index',
        ],
        'eCampApi\\V1\\Rpc\\Invitation\\InvitationController' => [
            'http_methods' => [
                0 => 'GET',
                1 => 'GET',
            ],
            'route_name' => 'e-camp-api.rpc.invitation',
        ],
        'eCampApi\\V1\\Rpc\\Invitation\\UpdateInvitationController' => [
            'http_methods' => [
                0 => 'POST',
                1 => 'POST',
            ],
            'route_name' => 'e-camp-api.rpc.invitation.accept',
        ],
        'eCampApi\\V1\\Rpc\\Printer\\PrinterController' => [
            'http_methods' => [
                0 => 'POST',
            ],
            'route_name' => 'e-camp-api.rpc.printer',
        ],
        'eCampApi\\V1\\Rpc\\Profile\\ProfileController' => [
            'http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
            ],
            'route_name' => 'e-camp-api.rpc.profile',
        ],
        'eCampApi\\V1\\Rpc\\Register\\RegisterController' => [
            'http_methods' => [
                0 => 'POST',
            ],
            'route_name' => 'e-camp-api.rpc.register',
        ],
    ],
];
