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
            'e-camp-api.rest.doctrine.camp-type' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/camp-types[/:campTypeId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\CampType\\Controller',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.activity-type' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/activity-types[/:activityTypeId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\ActivityType\\Controller',
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
            'e-camp-api.rest.doctrine.activity' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/activities[/:activityId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\Activity\\Controller',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.activity-category' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/activity-categories[/:activityCategoryId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\ActivityCategory\\Controller',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.activity-responsible' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/activity-responsibles[/:activityResponsibleId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\ActivityResponsible\\Controller',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.schedule-entry' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/schedule-entries[/:scheduleEntryId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\ScheduleEntry\\Controller',
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
            'e-camp-api.rest.doctrine.activity-content' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/activity-contents[/:activityContentId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\ActivityContent\\Controller',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.content-type' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/content-types[/:contentTypeId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\ContentType\\Controller',
                    ],
                ],
            ],
            'e-camp-api.rest.doctrine.activity-type-content-type' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api/activity-type-content-types[/:activityTypeContentTypeId]',
                    'defaults' => [
                        'controller' => 'eCampApi\\V1\\Rest\\ActivityTypeContentType\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            'eCampApi\\V1\\Rpc\\Auth\\AuthController' => \Laminas\Di\Container\ServiceManager\AutowireFactory::class,
            'eCampApi\\V1\\Rpc\\Index\\IndexController' => \Laminas\Di\Container\ServiceManager\AutowireFactory::class,
            'eCampApi\\V1\\Rpc\\Register\\RegisterController' => \Laminas\Di\Container\ServiceManager\AutowireFactory::class,
            'eCampApi\\V1\\Rpc\\Profile\\ProfileController' => \Laminas\Di\Container\ServiceManager\AutowireFactory::class,
            'eCampApi\\V1\\Rpc\\Printer\\PrinterController' => \Laminas\Di\Container\ServiceManager\AutowireFactory::class,
        ],
    ],
    'api-tools-versioning' => [
        /*
        'uri' => array(
            0 => 'e-camp-api.rest.doctrine.camp-type',
            1 => 'e-camp-api.rest.doctrine.activity-type',
            2 => 'e-camp-api.rest.doctrine.organization',
            3 => 'e-camp-api.rest.doctrine.camp',
            4 => 'e-camp-api.rest.doctrine.period',
            5 => 'e-camp-api.rest.doctrine.day',
            6 => 'e-camp-api.rest.doctrine.activity',
            7 => 'e-camp-api.rest.doctrine.activity-category',
            8 => 'e-camp-api.rest.doctrine.schedule-entry',
            9 => 'e-camp-api.rest.doctrine.user',
            10 => 'e-camp-api.rest.doctrine.camp-collaboration',
            12 => 'e-camp-api.rpc.index',
            11 => 'e-camp-api.rpc.auth',
            13 => 'e-camp-api.rpc.register',
            14 => 'e-camp-api.rest.doctrine.activity-content',
            15 => 'e-camp-api.rest.doctrine.content-type',
            16 => 'e-camp-api.rest.doctrine.activity-type-content-type',
        ),
        */
    ],
    'api-tools-rest' => [
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
        'eCampApi\\V1\\Rest\\ActivityType\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\ActivityTypeService',
            'route_name' => 'e-camp-api.rest.doctrine.activity-type',
            'route_identifier_name' => 'activityTypeId',
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
            'entity_class' => 'eCamp\\Core\\Entity\\ActivityType',
            'collection_class' => 'eCampApi\\V1\\Rest\\ActivityType\\ActivityTypeCollection',
            'service_name' => 'ActivityType',
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
        'eCampApi\\V1\\Rest\\Activity\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\ActivityService',
            'route_name' => 'e-camp-api.rest.doctrine.activity',
            'route_identifier_name' => 'activityId',
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
            'entity_class' => 'eCamp\\Core\\Entity\\Activity',
            'collection_class' => 'eCampApi\\V1\\Rest\\Activity\\ActivityCollection',
            'service_name' => 'Activity',
        ],
        'eCampApi\\V1\\Rest\\ActivityCategory\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\ActivityCategoryService',
            'route_name' => 'e-camp-api.rest.doctrine.activity-category',
            'route_identifier_name' => 'activityCategoryId',
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
            'entity_class' => 'eCamp\\Core\\Entity\\ActivityCategory',
            'collection_class' => 'eCampApi\\V1\\Rest\\ActivityCategory\\ActivityCategoryCollection',
            'service_name' => 'ActivityCategory',
        ],
        'eCampApi\\V1\\Rest\\ActivityResponsible\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\ActivityResponsibleService',
            'route_name' => 'e-camp-api.rest.doctrine.activity-responsible',
            'route_identifier_name' => 'activityResponsibleId',
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
            'entity_class' => 'eCamp\\Core\\Entity\\ActivityResponsible',
            'collection_class' => 'eCampApi\\V1\\Rest\\ActivityResponsible\\ActivityResponsibleCollection',
            'service_name' => 'ActivityResponsible',
        ],
        'eCampApi\\V1\\Rest\\ScheduleEntry\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\ScheduleEntryService',
            'route_name' => 'e-camp-api.rest.doctrine.schedule-entry',
            'route_identifier_name' => 'scheduleEntryId',
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
                0 => 'activityId',
                1 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => 'eCamp\\Core\\Entity\\ScheduleEntry',
            'collection_class' => 'eCampApi\\V1\\Rest\\ScheduleEntry\\ScheduleEntryCollection',
            'service_name' => 'ScheduleEntry',
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
        'eCampApi\\V1\\Rest\\ActivityContent\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\ActivityContentService',
            'route_name' => 'e-camp-api.rest.doctrine.activity-content',
            'route_identifier_name' => 'activityContentId',
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
                0 => 'activityId',
                1 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => 'eCamp\\Core\\Entity\\ActivityContent',
            'collection_class' => 'eCampApi\\V1\\Rest\\ActivityContent\\ActivityContentCollection',
            'service_name' => 'ActivityContent',
        ],
        'eCampApi\\V1\\Rest\\ContentType\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\ContentTypeService',
            'route_name' => 'e-camp-api.rest.doctrine.content-type',
            'route_identifier_name' => 'contentTypeId',
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
            'entity_class' => 'eCamp\\Core\\Entity\\ContentType',
            'collection_class' => 'eCampApi\\V1\\Rest\\ContentType\\ContentTypeCollection',
            'service_name' => 'ContentType',
        ],
        'eCampApi\\V1\\Rest\\ActivityTypeContentType\\Controller' => [
            'listener' => 'eCamp\\Core\\EntityService\\ActivityTypeContentTypeService',
            'route_name' => 'e-camp-api.rest.doctrine.activity-type-content-type',
            'route_identifier_name' => 'activityTypeContentTypeId',
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
                0 => 'activityTypeId',
                1 => 'page_size',
            ],
            'page_size' => -1,
            'page_size_param' => 'page_size',
            'entity_class' => 'eCamp\\Core\\Entity\\ActivityTypeContentType',
            'collection_class' => 'eCampApi\\V1\\Rest\\ActivityTypeContentType\\ActivityTypeContentTypeCollection',
            'service_name' => 'ActivityTypeContentType',
        ],
    ],
    'api-tools-content-negotiation' => [
        'controllers' => [
            'eCampApi\\V1\\Rpc\\Index\\IndexController' => 'HalJson',
            'eCampApi\\V1\\Rpc\\Auth\\AuthController' => 'HalJson',
            'eCampApi\\V1\\Rpc\\Register\\RegisterController' => 'HalJson',
            'eCampApi\\V1\\Rpc\\Profile\\ProfileController' => 'HalJson',
            'eCampApi\\V1\\Rpc\\Printer\\PrinterController' => 'HalJson',
            'eCampApi\\V1\\Rest\\CampType\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\ActivityType\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\Organization\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\Camp\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\Period\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\Day\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\Activity\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\ActivityCategory\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\ActivityResponsible\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\ScheduleEntry\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\User\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\CampCollaboration\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\ActivityContent\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\ContentType\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rest\\ActivityTypeContentType\\Controller' => 'HalJson',
        ],
        'accept_whitelist' => [
            'eCampApi\\V1\\Rpc\\Index\\IndexController' => [
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
            'eCampApi\\V1\\Rest\\CampType\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\ActivityType\\Controller' => [
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
            'eCampApi\\V1\\Rest\\Activity\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\ActivityCategory\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\ActivityResponsible\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\ScheduleEntry\\Controller' => [
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
            'eCampApi\\V1\\Rest\\ActivityContent\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\ContentType\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\ActivityTypeContentType\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content_type_whitelist' => [
            'eCampApi\\V1\\Rpc\\Index\\IndexController' => [
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
            'eCampApi\\V1\\Rest\\CampType\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\ActivityType\\Controller' => [
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
            'eCampApi\\V1\\Rest\\Activity\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\ActivityCategory\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\ActivityResponsible\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\ScheduleEntry\\Controller' => [
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
            'eCampApi\\V1\\Rest\\ActivityContent\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\ContentType\\Controller' => [
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ],
            'eCampApi\\V1\\Rest\\ActivityTypeContentType\\Controller' => [
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
            'eCamp\\Core\\Entity\\ActivityType' => [
                'route_identifier_name' => 'activityTypeId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.activity-type',
                'hydrator' => 'eCamp\\Core\\Hydrator\\ActivityTypeHydrator',
                'max_depth' => 20,
            ],
            'eCampApi\\V1\\Rest\\ActivityType\\ActivityTypeCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.activity-type',
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
            'eCamp\\Core\\Entity\\Activity' => [
                'route_identifier_name' => 'activityId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.activity',
                'hydrator' => 'eCamp\\Core\\Hydrator\\ActivityHydrator',
                'max_depth' => 20,
            ],
            'eCampApi\\V1\\Rest\\Activity\\ActivityCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.activity',
                'is_collection' => true,
                'max_depth' => 20,
            ],
            'eCamp\\Core\\Entity\\ActivityCategory' => [
                'route_identifier_name' => 'activityCategoryId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.activity-category',
                'hydrator' => 'eCamp\\Core\\Hydrator\\ActivityCategoryHydrator',
                'max_depth' => 20,
            ],
            'eCampApi\\V1\\Rest\\ActivityCategory\\ActivityCategoryCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.activity-category',
                'is_collection' => true,
                'max_depth' => 20,
            ],
            'eCamp\\Core\\Entity\\ActivityResponsible' => [
                'route_identifier_name' => 'activityResponsibleId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.activity-responsible',
                'hydrator' => 'eCamp\\Core\\Hydrator\\ActivityResponsibleHydrator',
                'max_depth' => 20,
            ],
            'eCampApi\\V1\\Rest\\ActivityResponsible\\ActivityResponsibleCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.activity-responsible',
                'is_collection' => true,
                'max_depth' => 20,
            ],
            'eCamp\\Core\\Entity\\ScheduleEntry' => [
                'route_identifier_name' => 'scheduleEntryId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.schedule-entry',
                'hydrator' => 'eCamp\\Core\\Hydrator\\ScheduleEntryHydrator',
                'max_depth' => 20,
            ],
            'eCampApi\\V1\\Rest\\ScheduleEntry\\ScheduleEntryCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.schedule-entry',
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
            'eCamp\\Core\\Entity\\ActivityContent' => [
                'route_identifier_name' => 'activityContentId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.activity-content',
                'hydrator' => 'eCamp\\Core\\Hydrator\\ActivityContentHydrator',
            ],
            'eCampApi\\V1\\Rest\\ActivityContent\\ActivityContentCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.activity-content',
                'is_collection' => true,
            ],
            'eCamp\\Core\\Entity\\ContentType' => [
                'route_identifier_name' => 'contentTypeId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.content-type',
                'hydrator' => 'eCamp\\Core\\Hydrator\\ContentTypeHydrator',
            ],
            'eCampApi\\V1\\Rest\\ContentType\\ContentTypeCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.content-type',
                'is_collection' => true,
            ],
            'eCamp\\Core\\Entity\\ActivityTypeContentType' => [
                'route_identifier_name' => 'activityTypeContentTypeId',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.activity-type-content-type',
                'hydrator' => 'eCamp\\Core\\Hydrator\\ActivityTypeContentTypeHydrator',
            ],
            'eCampApi\\V1\\Rest\\ActivityTypeContentType\\ActivityTypeContentTypeCollection' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.activity-type-content-type',
                'is_collection' => true,
            ],
        ],
    ],
    'api-tools-content-validation' => [
        'eCampApi\\V1\\Rpc\\Auth\\AuthController' => [
            'input_filter' => 'eCampApi\\V1\\Rpc\\Auth\\Validator',
        ],
        'eCampApi\\V1\\Rest\\CampType\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\CampType\\Validator',
        ],
        'eCampApi\\V1\\Rest\\ActivityType\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\ActivityType\\Validator',
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
        'eCampApi\\V1\\Rest\\Activity\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\Activity\\Validator',
        ],
        'eCampApi\\V1\\Rest\\ActivityCategory\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\ActivityCategory\\Validator',
        ],
        'eCampApi\\V1\\Rest\\ActivityResponsible\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\ActivityResponsible\\Validator',
        ],
        'eCampApi\\V1\\Rest\\ScheduleEntry\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\ScheduleEntry\\Validator',
        ],
        'eCampApi\\V1\\Rest\\User\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\User\\Validator',
        ],
        'eCampApi\\V1\\Rest\\CampCollaboration\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\CampCollaboration\\Validator',
        ],
        'eCampApi\\V1\\Rest\\ActivityContent\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\ActivityContent\\Validator',
        ],
        'eCampApi\\V1\\Rest\\ContentType\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\ContentType\\Validator',
        ],
        'eCampApi\\V1\\Rest\\ActivityTypeContentType\\Controller' => [
            'input_filter' => 'eCampApi\\V1\\Rest\\ActivityTypeContentType\\Validator',
        ],
    ],
    'input_filter_specs' => [
        'eCampApi\\V1\\Rpc\\Auth\\Validator' => [],
        'eCampApi\\V1\\Rest\\CampType\\Validator' => [
            0 => [
                'name' => 'name',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
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
        'eCampApi\\V1\\Rest\\ActivityType\\Validator' => [
            0 => [
                'name' => 'name',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
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
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
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
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
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
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
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
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
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
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
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
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
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
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
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
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\Digits',
                    ],
                ],
                'validators' => [],
            ],
        ],
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
        ],
        'eCampApi\\V1\\Rest\\ActivityCategory\\Validator' => [
            0 => [
                'name' => 'short',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
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
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
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
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
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
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 1,
                        ],
                    ],
                ],
            ],
            4 => [
                'name' => 'activityTypeId',
            ],
        ],
        'eCampApi\\V1\\Rest\\ActivityResponsible\\Validator' => [
        ],
        'eCampApi\\V1\\Rest\\ScheduleEntry\\Validator' => [
            0 => [
                'name' => 'start',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\Digits',
                    ],
                ],
                'validators' => [],
            ],
            1 => [
                'name' => 'length',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\Digits',
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
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
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
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
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
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
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
        ],
        'eCampApi\\V1\\Rest\\ActivityContent\\Validator' => [
            0 => [
                'name' => 'instanceName',
                'required' => false,
                'filters' => [
                    0 => [
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
            ],
        ],
        'eCampApi\\V1\\Rest\\ContentType\\Validator' => [
            0 => [
                'name' => 'name',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
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
                        'name' => 'Laminas\\Filter\\StringTrim',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => 'Laminas\\Validator\\StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 128,
                        ],
                    ],
                ],
            ],
        ],
        'eCampApi\\V1\\Rest\\ActivityTypeContentType\\Validator' => [
            0 => [
                'name' => 'minNumberContentTypeInstances',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\Digits',
                    ],
                ],
                'validators' => [],
            ],
            1 => [
                'name' => 'maxNumberContentTypeInstances',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => 'Laminas\\Filter\\StripTags',
                    ],
                    1 => [
                        'name' => 'Laminas\\Filter\\Digits',
                    ],
                ],
                'validators' => [],
            ],
            2 => [
                'name' => 'jsonConfig',
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
    'api-tools' => [
        'doctrine-connected' => [],
    ],
    'doctrine-hydrator' => [],
];
