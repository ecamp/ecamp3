<?php

return [
    'router' => [
        'routes' => [
            'e-camp-api.rpc.index' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/[:action]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rpc\\Index\\Controller',
                        'action' => 'index',
                    ],
                ],
            ],
            'e-camp-api.rpc.auth' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/auth[/:action]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rpc\\Auth\\Controller',
                        'action' => 'index',
                    ],
                ],
            ],
            'e-camp-api.rpc.register' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/register[/:action]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rpc\\Register\\Controller',
                        'action' => 'register',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.camp-type' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/camp-types[/:campTypeId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\CampType\\Controller',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.event-type' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/event-types[/:eventTypeId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\EventType\\Controller',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.organization' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/organizations[/:organizationId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\Organization\\Controller',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.camp' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/camps[/:campId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\Camp\\Controller',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.period' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/periods[/:periodId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\Period\\Controller',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.day' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/days[/:dayId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\Day\\Controller',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.event' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/events[/:eventId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\Event\\Controller',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.event-category' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/event-categories[/:eventCategoryId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\EventCategory\\Controller',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.event-instance' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/event-instances[/:eventInstanceId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\EventInstance\\Controller',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.user' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/users[/:userId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\User\\Controller',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.camp-collaboration' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/camp-collaborations[/:campCollaborationId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\CampCollaboration\\Controller',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.event-plugin' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/event-plugins[/:eventPluginId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\EventPlugin\\Controller',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.plugin' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/plugins[/:pluginId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\Plugin\\Controller',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.event-type-plugin' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/event-type-plugins[/:event_type_pluginId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\EventTypePlugin\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            'eCampApi\\V1\\Rpc\\Auth\\Controller' => 'eCampApi\\V1\\Rpc\\Auth\\AuthControllerFactory',
            'eCampApi\\V1\\Rpc\\Index\\Controller' => 'eCampApi\\V1\\Rpc\\Index\\IndexControllerFactory',
            'eCampApi\\V1\\Rpc\\Register\\Controller' => 'eCampApi\\V1\\Rpc\\Register\\RegisterControllerFactory',
        ],
    ],
    'zf-versioning' => [
        /*
        'uri' => array(
            0 => 'e-camp-api.rest.doctrine.camp-type',
            1 => 'e-camp-api.rest.doctrine.event-type',
            2 => 'e-camp-api.rest.doctrine.organization',
            3 => 'e-camp-api.rest.doctrine.camp',
            4 => 'e-camp-api.rest.doctrine.period',
            5 => 'e-camp-api.rest.doctrine.day',
            6 => 'e-camp-api.rest.doctrine.event',
            7 => 'e-camp-api.rest.doctrine.event-category',
            8 => 'e-camp-api.rest.doctrine.event-instance',
            9 => 'e-camp-api.rest.doctrine.user',
            10 => 'e-camp-api.rest.doctrine.camp-collaboration',
            12 => 'e-camp-api.rpc.index',
            11 => 'e-camp-api.rpc.auth',
            13 => 'e-camp-api.rpc.register',
            14 => 'e-camp-api.rest.doctrine.event-plugin',
            15 => 'e-camp-api.rest.doctrine.plugin',
            16 => 'e-camp-api.rest.doctrine.event-type-plugin',
        ),
        */
    ],
    'zf-rest' => [
        'eCampApi\\V1\\Rest\\CampType\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\CampTypeService',
            'route_name' => 'e-camp-api.rest.doctrine.camp-type',
            'route_identifier_name' => 'campTypeId',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
            ],
            'collection_http_methods' => [
                0 => 'GET',
            ],
            'collection_query_whitelist' => [
                0 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => 'eCamp\\Core\\Entity\\CampType',
            'collection_class' => 'eCampApi\\V1\\Rest\\CampType\\CampTypeCollection',
            'service_name' => 'CampType',
        ],
        'eCampApi\\V1\\Rest\\EventType\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\EventTypeService',
            'route_name' => 'e-camp-api.rest.doctrine.event-type',
            'route_identifier_name' => 'eventTypeId',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
            ],
            'collection_http_methods' => [
                0 => 'GET',
            ],
            'collection_query_whitelist' => [
                0 => 'campTypeId',
                1 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => 'eCamp\\Core\\Entity\\EventType',
            'collection_class' => 'eCampApi\\V1\\Rest\\EventType\\EventTypeCollection',
            'service_name' => 'EventType',
        ],
        'eCampApi\\V1\\Rest\\Organization\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\OrganizationService',
            'route_name' => 'e-camp-api.rest.doctrine.organization',
            'route_identifier_name' => 'organizationId',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [
                0 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => 'eCamp\\Core\\Entity\\Organization',
            'collection_class' => 'eCampApi\\V1\\Rest\\Organization\\OrganizationCollection',
            'service_name' => 'Organization',
        ],
        'eCampApi\\V1\\Rest\\Camp\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\CampService',
            'route_name' => 'e-camp-api.rest.doctrine.camp',
            'route_identifier_name' => 'campId',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [
                0 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => 'eCamp\\Core\\Entity\\Camp',
            'collection_class' => 'eCampApi\\V1\\Rest\\Camp\\CampCollection',
            'service_name' => 'Camp',
        ],
        'eCampApi\\V1\\Rest\\Period\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\PeriodService',
            'route_name' => 'e-camp-api.rest.doctrine.period',
            'route_identifier_name' => 'periodId',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [
                0 => 'campId',
                1 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => 'eCamp\\Core\\Entity\\Period',
            'collection_class' => 'eCampApi\\V1\\Rest\\Period\\PeriodCollection',
            'service_name' => 'Period',
        ],
        'eCampApi\\V1\\Rest\\Day\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\DayService',
            'route_name' => 'e-camp-api.rest.doctrine.day',
            'route_identifier_name' => 'dayId',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [
                0 => 'campId',
                1 => 'periodId',
                2 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => 'eCamp\\Core\\Entity\\Day',
            'collection_class' => 'eCampApi\\V1\\Rest\\Day\\DayCollection',
            'service_name' => 'Day',
        ],
        'eCampApi\\V1\\Rest\\Event\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\EventService',
            'route_name' => 'e-camp-api.rest.doctrine.event',
            'route_identifier_name' => 'eventId',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [
                0 => 'campId',
                1 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => 'eCamp\\Core\\Entity\\Event',
            'collection_class' => 'eCampApi\\V1\\Rest\\Event\\EventCollection',
            'service_name' => 'Event',
        ],
        'eCampApi\\V1\\Rest\\EventCategory\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\EventCategoryService',
            'route_name' => 'e-camp-api.rest.doctrine.event-category',
            'route_identifier_name' => 'eventCategoryId',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [
                0 => 'campId',
                1 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => 'eCamp\\Core\\Entity\\EventCategory',
            'collection_class' => 'eCampApi\\V1\\Rest\\EventCategory\\EventCategoryCollection',
            'service_name' => 'EventCategory',
        ],
        'eCampApi\\V1\\Rest\\EventInstance\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\EventInstanceService',
            'route_name' => 'e-camp-api.rest.doctrine.event-instance',
            'route_identifier_name' => 'eventInstanceId',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [
                0 => 'eventId',
                1 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => 'eCamp\\Core\\Entity\\EventInstance',
            'collection_class' => 'eCampApi\\V1\\Rest\\EventInstance\\EventInstanceCollection',
            'service_name' => 'EventInstance',
        ],
        'eCampApi\\V1\\Rest\\User\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\UserService',
            'route_name' => 'e-camp-api.rest.doctrine.user',
            'route_identifier_name' => 'userId',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [
                0 => 'search',
                1 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => 'eCamp\\Core\\Entity\\User',
            'collection_class' => 'eCampApi\\V1\\Rest\\User\\UserCollection',
            'service_name' => 'User',
        ],
        'eCampApi\\V1\\Rest\\CampCollaboration\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\CampCollaborationService',
            'route_name' => 'e-camp-api.rest.doctrine.camp-collaboration',
            'route_identifier_name' => 'campCollaborationId',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [
                0 => 'campId',
                1 => 'userId',
                2 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => 'eCamp\\Core\\Entity\\CampCollaboration',
            'collection_class' => 'eCampApi\\V1\\Rest\\CampCollaboration\\CampCollaborationCollection',
            'service_name' => 'CampCollaboration',
        ],
        'eCampApi\\V1\\Rest\\EventPlugin\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\EventPluginService',
            'route_name' => 'e-camp-api.rest.doctrine.event-plugin',
            'route_identifier_name' => 'eventPluginId',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'DELETE',
                3 => 'PUT',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [
                0 => 'eventId',
                1 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => 'eCamp\\Core\\Entity\\EventPlugin',
            'collection_class' => 'eCampApi\\V1\\Rest\\EventPlugin\\EventPluginCollection',
            'service_name' => 'EventPlugin',
        ],
        'eCampApi\\V1\\Rest\\Plugin\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\PluginService',
            'route_name' => 'e-camp-api.rest.doctrine.plugin',
            'route_identifier_name' => 'pluginId',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
            ],
            'collection_http_methods' => [
                0 => 'GET',
            ],
            'collection_query_whitelist' => [
                0 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => 'eCamp\\Core\\Entity\\Plugin',
            'collection_class' => 'eCampApi\\V1\\Rest\\Plugin\\PluginCollection',
            'service_name' => 'Plugin',
        ],
        'eCampApi\\V1\\Rest\\EventTypePlugin\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\EventTypePluginService',
            'route_name' => 'e-camp-api.rest.doctrine.event-type-plugin',
            'route_identifier_name' => 'event_type_pluginId',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [
                0 => 'eventTypeId',
                1 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => 'eCamp\\Core\\Entity\\EventTypePlugin',
            'collection_class' => 'eCampApi\\V1\\Rest\\EventTypePlugin\\EventTypePluginCollection',
            'service_name' => 'EventTypePlugin',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers' => [
            'eCampApi\\V1\\Rest\\CampType\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\EventType\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\Organization\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\Camp\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\Period\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\Day\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\Event\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\EventCategory\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\EventInstance\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\User\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\CampCollaboration\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rpc\\Index\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rpc\\Auth\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rpc\\Register\\Controller' => 'Json',
            'eCampApi\\V1\\Rest\\EventPlugin\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\Plugin\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\EventTypePlugin\\Controller' => 'HalJson',
        ],
        'accept_whitelist' => [
            'eCampApi\\V1\\Rest\\CampType\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\EventType\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\Organization\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\Camp\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\Period\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\Day\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\Event\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\EventCategory\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\EventInstance\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\User\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\CampCollaboration\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'eCampApi\\V1\\Rpc\\Index\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ],
            'eCampApi\\V1\\Rpc\\Auth\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ],
            'eCampApi\\V1\\Rpc\\Register\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ],
            'eCampApi\\V1\\Rest\\EventPlugin\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\Plugin\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\EventTypePlugin\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content_type_whitelist' => [
            'eCampApi\\V1\\Rest\\CampType\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\EventType\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\Organization\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\Camp\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\Period\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\Day\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\Event\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\EventCategory\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\EventInstance\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\User\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\CampCollaboration\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rpc\\Index\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rpc\\Auth\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rpc\\Register\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\EventPlugin\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\Plugin\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\EventTypePlugin\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
        ],
    ],
    'zf-hal' => [
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
            //            \eCamp\Lib\Entity\CollectionLink::class => array(
            //                'route_identifier_name' => 'id',
            //                'entity_identifier_name' => 'id',
            //                'route_name' => '',
            //            ),

            'eCamp\\Core\\Entity\\CampType' => [
                'route_identifier_name' => 'campTypeId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.camp-type',
                'hydrator' => 'eCamp\\Core\\Hydrator\\CampTypeHydrator',
                'max_depth' => 20,
            ],
            'eCampApi\\V1\\Rest\\CampType\\CampTypeCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.camp-type',
                'is_collection' => true,
                'max_depth' => 20,
            ],
            'eCamp\\Core\\Entity\\EventType' => [
                'route_identifier_name' => 'eventTypeId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event-type',
                'hydrator' => 'eCamp\\Core\\Hydrator\\EventTypeHydrator',
                'max_depth' => 20,
            ],
            'eCampApi\\V1\\Rest\\EventType\\EventTypeCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event-type',
                'is_collection' => true,
                'max_depth' => 20,
            ],
            'eCamp\\Core\\Entity\\Organization' => [
                'route_identifier_name' => 'organizationId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.organization',
                'hydrator' => 'eCamp\\Core\\Hydrator\\OrganizationHydrator',
                'max_depth' => 20,
            ],
            'eCampApi\\V1\\Rest\\Organization\\OrganizationCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.organization',
                'is_collection' => true,
                'max_depth' => 20,
            ],
            'eCamp\\Core\\Entity\\Camp' => [
                'route_identifier_name' => 'campId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.camp',
                'hydrator' => 'eCamp\\Core\\Hydrator\\CampHydrator',
                'max_depth' => 20,
            ],
            'eCampApi\\V1\\Rest\\Camp\\CampCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.camp',
                'is_collection' => true,
                'max_depth' => 20,
            ],
            'eCamp\\Core\\Entity\\Period' => [
                'route_identifier_name' => 'periodId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.period',
                'hydrator' => 'eCamp\\Core\\Hydrator\\PeriodHydrator',
                'max_depth' => 20,
            ],
            'eCampApi\\V1\\Rest\\Period\\PeriodCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.period',
                'is_collection' => true,
                'max_depth' => 20,
            ],
            'eCamp\\Core\\Entity\\Day' => [
                'route_identifier_name' => 'dayId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.day',
                'hydrator' => 'eCamp\\Core\\Hydrator\\DayHydrator',
                'max_depth' => 20,
            ],
            'eCampApi\\V1\\Rest\\Day\\DayCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.day',
                'is_collection' => true,
                'max_depth' => 20,
            ],
            'eCamp\\Core\\Entity\\Event' => [
                'route_identifier_name' => 'eventId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event',
                'hydrator' => 'eCamp\\Core\\Hydrator\\EventHydrator',
                'max_depth' => 20,
            ],
            'eCampApi\\V1\\Rest\\Event\\EventCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event',
                'is_collection' => true,
                'max_depth' => 20,
            ],
            'eCamp\\Core\\Entity\\EventCategory' => [
                'route_identifier_name' => 'eventCategoryId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event-category',
                'hydrator' => 'eCamp\\Core\\Hydrator\\EventCategoryHydrator',
                'max_depth' => 20,
            ],
            'eCampApi\\V1\\Rest\\EventCategory\\EventCategoryCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event-category',
                'is_collection' => true,
                'max_depth' => 20,
            ],
            'eCamp\\Core\\Entity\\EventInstance' => [
                'route_identifier_name' => 'eventInstanceId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event-instance',
                'hydrator' => 'eCamp\\Core\\Hydrator\\EventInstanceHydrator',
                'max_depth' => 20,
            ],
            'eCampApi\\V1\\Rest\\EventInstance\\EventInstanceCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event-instance',
                'is_collection' => true,
                'max_depth' => 20,
            ],
            'eCamp\\Core\\Entity\\User' => [
                'route_identifier_name' => 'userId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.user',
                'hydrator' => 'eCamp\\Core\\Hydrator\\UserHydrator',
                'max_depth' => 20,
            ],
            'eCampApi\\V1\\Rest\\User\\UserCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.user',
                'is_collection' => true,
                'max_depth' => 20,
            ],
            'eCamp\\Core\\Entity\\CampCollaboration' => [
                'route_identifier_name' => 'campCollaborationId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.camp-collaboration',
                'hydrator' => 'eCamp\\Core\\Hydrator\\CampCollaborationHydrator',
                'max_depth' => 20,
            ],
            'eCampApi\\V1\\Rest\\CampCollaboration\\CampCollaborationCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.camp-collaboration',
                'is_collection' => true,
                'max_depth' => 20,
            ],
            'eCamp\\Core\\Entity\\EventPlugin' => [
                'route_identifier_name' => 'eventPluginId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event-plugin',
                'hydrator' => 'eCamp\\Core\\Hydrator\\EventPluginHydrator',
            ],
            'eCampApi\\V1\\Rest\\EventPlugin\\EventPluginCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event-plugin',
                'is_collection' => true,
            ],
            'eCamp\\Core\\Entity\\Plugin' => [
                'route_identifier_name' => 'pluginId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.plugin',
                'hydrator' => 'eCamp\\Core\\Hydrator\\PluginHydrator',
            ],
            'eCampApi\\V1\\Rest\\Plugin\\PluginCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.plugin',
                'is_collection' => true,
            ],
            'eCamp\\Core\\Entity\\EventTypePlugin' => [
                'route_identifier_name' => 'event_type_pluginId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event-type-plugin',
                'hydrator' => 'eCamp\\Core\\Hydrator\\EventTypePluginHydrator',
            ],
            'eCampApi\\V1\\Rest\\EventTypePlugin\\EventTypePluginCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event-type-plugin',
                'is_collection' => true,
            ],
        ],
    ],
    'zf-content-validation' => [
        'eCampApi\\V1\\Rest\\CampType\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\CampType\\Validator',
        ],
        'eCampApi\\V1\\Rest\\EventType\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\EventType\\Validator',
        ],
        'eCampApi\\V1\\Rest\\Organization\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\Organization\\Validator',
        ],
        'eCampApi\\V1\\Rest\\Camp\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\Camp\\Validator',
        ],
        'eCampApi\\V1\\Rest\\Period\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\Period\\Validator',
        ],
        'eCampApi\\V1\\Rest\\Day\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\Day\\Validator',
        ],
        'eCampApi\\V1\\Rest\\Event\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\Event\\Validator',
        ],
        'eCampApi\\V1\\Rest\\EventCategory\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\EventCategory\\Validator',
        ],
        'eCampApi\\V1\\Rest\\EventInstance\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\EventInstance\\Validator',
        ],
        'eCampApi\\V1\\Rest\\User\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\User\\Validator',
        ],
        'eCampApi\\V1\\Rest\\CampCollaboration\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\CampCollaboration\\Validator',
        ],
        'eCampApi\\V1\\Rpc\\Auth\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rpc\\Auth\\Validator',
        ],
        'eCampApi\\V1\\Rest\\EventPlugin\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\EventPlugin\\Validator',
        ],
        'eCampApi\\V1\\Rest\\Plugin\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\Plugin\\Validator',
        ],
        'eCampApi\\V1\\Rest\\EventTypePlugin\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\EventTypePlugin\\Validator',
        ],
    ],
    'input_filter_specs' => [
        'eCampApi\\V1\\Rest\\CampType\\Validator' => [
            0 => [
                'name' => 'name',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 64,
                        ],
                    ],
                ],
            ],
            1 => [
                'name' => 'isJS',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
            2 => [
                'name' => 'isCourse',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
            3 => [
                'name' => 'jsonConfig',
                'required' => false,
                'filters' => [],
                'validators' => [],
            ],
        ],
        'eCampApi\\V1\\Rest\\EventType\\Validator' => [
            0 => [
                'name' => 'name',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 64,
                        ],
                    ],
                ],
            ],
            1 => [
                'name' => 'defaultColor',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 8,
                        ],
                    ],
                ],
            ],
            2 => [
                'name' => 'defaultNumberingStyle',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 1,
                        ],
                    ],
                ],
            ],
        ],
        'eCampApi\\V1\\Rest\\Organization\\Validator' => [
            0 => [
                'name' => 'name',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 64,
                        ],
                    ],
                ],
            ],
        ],
        'eCampApi\\V1\\Rest\\Camp\\Validator' => [
            0 => [
                'name' => 'name',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 32,
                        ],
                    ],
                ],
            ],
            1 => [
                'name' => 'title',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => [
                            'min' => 10,
                            'max' => 64,
                        ],
                    ],
                ],
            ],
            2 => [
                'name' => 'motto',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 128,
                        ],
                    ],
                ],
            ],
        ],
        'eCampApi\\V1\\Rest\\Period\\Validator' => [
            0 => [
                'name' => 'start',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
            1 => [
                'name' => 'end',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
            2 => [
                'name' => 'description',
                'required' => false,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 128,
                        ],
                    ],
                ],
            ],
        ],
        'eCampApi\\V1\\Rest\\Day\\Validator' => [
            0 => [
                'name' => 'dayOffset',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\Digits',
                    ],
                ],
                'validators' => [],
            ],
        ],
        'eCampApi\\V1\\Rest\\Event\\Validator' => [
            0 => [
                'name' => 'title',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
        ],
        'eCampApi\\V1\\Rest\\EventCategory\\Validator' => [
            0 => [
                'name' => 'short',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 16,
                        ],
                    ],
                ],
            ],
            1 => [
                'name' => 'name',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 64,
                        ],
                    ],
                ],
            ],
            2 => [
                'name' => 'color',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 8,
                        ],
                    ],
                ],
            ],
            3 => [
                'name' => 'numberingStyle',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 1,
                        ],
                    ],
                ],
            ],
        ],
        'eCampApi\\V1\\Rest\\EventInstance\\Validator' => [
            0 => [
                'name' => 'start',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\Digits',
                    ],
                ],
                'validators' => [],
            ],
            1 => [
                'name' => 'length',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\Digits',
                    ],
                ],
                'validators' => [],
            ],
            2 => [
                'name' => 'left',
                'required' => false,
                'filters' => [],
                'validators' => [],
            ],
            3 => [
                'name' => 'width',
                'required' => false,
                'filters' => [],
                'validators' => [],
            ],
        ],
        'eCampApi\\V1\\Rest\\User\\Validator' => [
            0 => [
                'name' => 'username',
                'required' => false,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 32,
                        ],
                    ],
                ],
            ],
            1 => [
                'name' => 'state',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 16,
                        ],
                    ],
                ],
            ],
            2 => [
                'name' => 'role',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 16,
                        ],
                    ],
                ],
            ],
        ],
        'eCampApi\\V1\\Rest\\CampCollaboration\\Validator' => [
            0 => [
                'name' => 'status',
                'required' => false,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [],
            ],
            1 => [
                'name' => 'role',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [],
            ],
            2 => [
                'name' => 'collaborationAcceptedBy',
                'required' => false,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [],
            ],
        ],
        'eCampApi\\V1\\Rpc\\Auth\\Validator' => [],
        'eCampApi\\V1\\Rest\\EventPlugin\\Validator' => [
            0 => [
                'name' => 'instance_name',
                'required' => false,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
            ],
        ],
        'eCampApi\\V1\\Rest\\Plugin\\Validator' => [
            0 => [
                'name' => 'name',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 64,
                        ],
                    ],
                ],
            ],
            1 => [
                'name' => 'active',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
            2 => [
                'name' => 'strategyClass',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 128,
                        ],
                    ],
                ],
            ],
        ],
        'eCampApi\\V1\\Rest\\EventTypePlugin\\Validator' => [
            0 => [
                'name' => 'minNumberPluginInstances',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\Digits',
                    ],
                ],
                'validators' => [],
            ],
            1 => [
                'name' => 'maxNumberPluginInstances',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\Digits',
                    ],
                ],
                'validators' => [],
            ],
            2 => [
                'name' => 'jsonConfig',
                'required' => false,
                'filters' => [
                    0 => [
                        'name' => 'Zend\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Zend\\Filter\\StripTags',
                    ],
                ],
                'validators' => [],
            ],
        ],
    ],
    'zf-rpc' => [
        'eCampApi\\V1\\Rpc\\Auth\\Controller' => [
            'service_name' => 'Auth',
            'http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'route_name' => 'e-camp-api.rpc.auth',
        ],
        'eCampApi\\V1\\Rpc\\Index\\Controller' => [
            'service_name' => 'Index',
            'http_methods' => [
                0 => 'GET',
            ],
            'route_name' => 'e-camp-api.rpc.index',
        ],
        'eCampApi\\V1\\Rpc\\Register\\Controller' => [
            'service_name' => 'Register',
            'http_methods' => [
                0 => 'POST',
            ],
            'route_name' => 'e-camp-api.rpc.register',
        ],
    ],
    'zf-apigility' => [
        'doctrine-connected' => [],
    ],
    'doctrine-hydrator' => [],
];
