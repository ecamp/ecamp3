<?php
return array(
    'router' => array(
        'routes' => array(
            'e-camp-api.rest.doctrine.camp-type' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/camp-type[/:camp_type_id]',
                    'defaults' => array(
                        'controller' => 'eCampApi\\V1\\Rest\\CampType\\Controller',
                    ),
                ),
            ),
            'e-camp-api.rest.doctrine.event-type' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/event-type[/:event_type_id]',
                    'defaults' => array(
                        'controller' => 'eCampApi\\V1\\Rest\\EventType\\Controller',
                    ),
                ),
            ),
            'e-camp-api.rest.doctrine.organization' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/organization[/:organization_id]',
                    'defaults' => array(
                        'controller' => 'eCampApi\\V1\\Rest\\Organization\\Controller',
                    ),
                ),
            ),
            'e-camp-api.rest.doctrine.camp' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/camp[/:camp_id]',
                    'defaults' => array(
                        'controller' => 'eCampApi\\V1\\Rest\\Camp\\Controller',
                    ),
                ),
            ),
            'e-camp-api.rest.doctrine.period' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/period[/:period_id]',
                    'defaults' => array(
                        'controller' => 'eCampApi\\V1\\Rest\\Period\\Controller',
                    ),
                ),
            ),
            'e-camp-api.rest.doctrine.day' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/day[/:day_id]',
                    'defaults' => array(
                        'controller' => 'eCampApi\\V1\\Rest\\Day\\Controller',
                    ),
                ),
            ),
            'e-camp-api.rest.doctrine.event' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/event[/:event_id]',
                    'defaults' => array(
                        'controller' => 'eCampApi\\V1\\Rest\\Event\\Controller',
                    ),
                ),
            ),
            'e-camp-api.rest.doctrine.event-category' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/event-category[/:event_category_id]',
                    'defaults' => array(
                        'controller' => 'eCampApi\\V1\\Rest\\EventCategory\\Controller',
                    ),
                ),
            ),
            'e-camp-api.rest.doctrine.event-instance' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/event-instance[/:event_instance_id]',
                    'defaults' => array(
                        'controller' => 'eCampApi\\V1\\Rest\\EventInstance\\Controller',
                    ),
                ),
            ),
            'e-camp-api.rest.doctrine.user' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/user[/:user_id]',
                    'defaults' => array(
                        'controller' => 'eCampApi\\V1\\Rest\\User\\Controller',
                    ),
                ),
            ),
            'e-camp-api.rest.doctrine.camp-collaboration' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/camp-collaboration[/:camp_collaboration_id]',
                    'defaults' => array(
                        'controller' => 'eCampApi\\V1\\Rest\\CampCollaboration\\Controller',
                    ),
                ),
            ),
            'e-camp-api.rpc.index' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api',
                    'defaults' => array(
                        'controller' => 'eCampApi\\V1\\Rpc\\Index\\Controller',
                        'action' => 'index',
                    ),
                ),
            ),
            'e-camp-api.rpc.login' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/login[/:action]',
                    'defaults' => array(
                        'controller' => 'eCampApi\\V1\\Rpc\\Login\\Controller',
                        'action' => 'index',
                    ),
                ),
            ),
            'e-camp-api.rpc.register' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/register[/:action]',
                    'defaults' => array(
                        'controller' => 'eCampApi\\V1\\Rpc\\Register\\Controller',
                        'action' => 'register',
                    ),
                ),
            ),
        ),
    ),
    'zf-versioning' => array(
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
            11 => 'e-camp-api.rpc.login',
            13 => 'e-camp-api.rpc.register',
        ),
    ),
    'zf-rest' => array(
        'eCampApi\\V1\\Rest\\CampType\\Controller' => array(
            'listener' => 'eCamp\\Core\\EntityService\\CampTypeService',
            'route_name' => 'e-camp-api.rest.doctrine.camp-type',
            'route_identifier_name' => 'camp_type_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => array(
                0 => 'GET',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'eCamp\\Core\\Entity\\CampType',
            'collection_class' => 'eCampApi\\V1\\Rest\\CampType\\CampTypeCollection',
            'service_name' => 'CampType',
        ),
        'eCampApi\\V1\\Rest\\EventType\\Controller' => array(
            'listener' => 'eCamp\\Core\\EntityService\\EventTypeService',
            'route_name' => 'e-camp-api.rest.doctrine.event-type',
            'route_identifier_name' => 'event_type_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => array(
                0 => 'GET',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
            ),
            'collection_query_whitelist' => array(
                0 => 'camp_type_id',
            ),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'eCamp\\Core\\Entity\\EventType',
            'collection_class' => 'eCampApi\\V1\\Rest\\EventType\\EventTypeCollection',
            'service_name' => 'EventType',
        ),
        'eCampApi\\V1\\Rest\\Organization\\Controller' => array(
            'listener' => 'eCamp\\Core\\EntityService\\OrganizationService',
            'route_name' => 'e-camp-api.rest.doctrine.organization',
            'route_identifier_name' => 'organization_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'eCamp\\Core\\Entity\\Organization',
            'collection_class' => 'eCampApi\\V1\\Rest\\Organization\\OrganizationCollection',
            'service_name' => 'Organization',
        ),
        'eCampApi\\V1\\Rest\\Camp\\Controller' => array(
            'listener' => 'eCamp\\Core\\EntityService\\CampService',
            'route_name' => 'e-camp-api.rest.doctrine.camp',
            'route_identifier_name' => 'camp_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'eCamp\\Core\\Entity\\Camp',
            'collection_class' => 'eCampApi\\V1\\Rest\\Camp\\CampCollection',
            'service_name' => 'Camp',
        ),
        'eCampApi\\V1\\Rest\\Period\\Controller' => array(
            'listener' => 'eCamp\\Core\\EntityService\\PeriodService',
            'route_name' => 'e-camp-api.rest.doctrine.period',
            'route_identifier_name' => 'period_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(
                0 => 'camp_id',
            ),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'eCamp\\Core\\Entity\\Period',
            'collection_class' => 'eCampApi\\V1\\Rest\\Period\\PeriodCollection',
            'service_name' => 'Period',
        ),
        'eCampApi\\V1\\Rest\\Day\\Controller' => array(
            'listener' => 'eCamp\\Core\\EntityService\\DayService',
            'route_name' => 'e-camp-api.rest.doctrine.day',
            'route_identifier_name' => 'day_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(
                0 => 'camp_id',
                1 => 'period_id',
            ),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'eCamp\\Core\\Entity\\Day',
            'collection_class' => 'eCampApi\\V1\\Rest\\Day\\DayCollection',
            'service_name' => 'Day',
        ),
        'eCampApi\\V1\\Rest\\Event\\Controller' => array(
            'listener' => 'eCamp\\Core\\EntityService\\EventService',
            'route_name' => 'e-camp-api.rest.doctrine.event',
            'route_identifier_name' => 'event_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(
                0 => 'camp_id',
            ),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'eCamp\\Core\\Entity\\Event',
            'collection_class' => 'eCampApi\\V1\\Rest\\Event\\EventCollection',
            'service_name' => 'Event',
        ),
        'eCampApi\\V1\\Rest\\EventCategory\\Controller' => array(
            'listener' => 'eCamp\\Core\\EntityService\\EventCategoryService',
            'route_name' => 'e-camp-api.rest.doctrine.event-category',
            'route_identifier_name' => 'event_category_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(
                0 => 'camp_id',
            ),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'eCamp\\Core\\Entity\\EventCategory',
            'collection_class' => 'eCampApi\\V1\\Rest\\EventCategory\\EventCategoryCollection',
            'service_name' => 'EventCategory',
        ),
        'eCampApi\\V1\\Rest\\EventInstance\\Controller' => array(
            'listener' => 'eCamp\\Core\\EntityService\\EventInstanceService',
            'route_name' => 'e-camp-api.rest.doctrine.event-instance',
            'route_identifier_name' => 'event_instance_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(
                0 => 'event_id',
            ),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'eCamp\\Core\\Entity\\EventInstance',
            'collection_class' => 'eCampApi\\V1\\Rest\\EventInstance\\EventInstanceCollection',
            'service_name' => 'EventInstance',
        ),
        'eCampApi\\V1\\Rest\\User\\Controller' => array(
            'listener' => 'eCamp\\Core\\EntityService\\UserService',
            'route_name' => 'e-camp-api.rest.doctrine.user',
            'route_identifier_name' => 'user_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'eCamp\\Core\\Entity\\User',
            'collection_class' => 'eCampApi\\V1\\Rest\\User\\UserCollection',
            'service_name' => 'User',
        ),
        'eCampApi\\V1\\Rest\\CampCollaboration\\Controller' => array(
            'listener' => 'eCamp\\Core\\EntityService\\CampCollaborationService',
            'route_name' => 'e-camp-api.rest.doctrine.camp-collaboration',
            'route_identifier_name' => 'camp_collaboration_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'items',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'collection_query_whitelist' => array(
                0 => 'camp_id',
                1 => 'user_id',
            ),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'eCamp\\Core\\Entity\\CampCollaboration',
            'collection_class' => 'eCampApi\\V1\\Rest\\CampCollaboration\\CampCollaborationCollection',
            'service_name' => 'CampCollaboration',
        ),
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
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
            'eCampApi\\V1\\Rpc\\Login\\Controller' => 'HalJson',
            'eCampApi\\V1\\Rpc\\Register\\Controller' => 'Json',
        ),
        'accept_whitelist' => array(
            'eCampApi\\V1\\Rest\\CampType\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\EventType\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\Organization\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\Camp\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\Period\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\Day\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\Event\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\EventCategory\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\EventInstance\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\User\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\CampCollaboration\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'eCampApi\\V1\\Rpc\\Index\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ),
            'eCampApi\\V1\\Rpc\\Login\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ),
            'eCampApi\\V1\\Rpc\\Register\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ),
        ),
        'content_type_whitelist' => array(
            'eCampApi\\V1\\Rest\\CampType\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\EventType\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\Organization\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\Camp\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\Period\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\Day\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\Event\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\EventCategory\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\EventInstance\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\User\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ),
            'eCampApi\\V1\\Rest\\CampCollaboration\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ),
            'eCampApi\\V1\\Rpc\\Index\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ),
            'eCampApi\\V1\\Rpc\\Login\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ),
            'eCampApi\\V1\\Rpc\\Register\\Controller' => array(
                0 => 'application/vnd.e-camp-api.v1+json',
                1 => 'application/json',
            ),
        ),
    ),
    'zf-hal' => array(
        'metadata_map' => array(
            'eCamp\\Core\\Entity\\CampType' => array(
                'route_identifier_name' => 'camp_type_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.camp-type',
                'hydrator' => 'eCamp\\Core\\Hydrator\\CampTypeHydrator',
                'max_depth' => 2,
            ),
            'eCampApi\\V1\\Rest\\CampType\\CampTypeCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.camp-type',
                'is_collection' => true,
                'max_depth' => 2,
            ),
            'eCamp\\Core\\Entity\\EventType' => array(
                'route_identifier_name' => 'event_type_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event-type',
                'hydrator' => 'eCamp\\Core\\Hydrator\\EventTypeHydrator',
                'max_depth' => 2,
            ),
            'eCampApi\\V1\\Rest\\EventType\\EventTypeCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event-type',
                'is_collection' => true,
                'max_depth' => 2,
            ),
            'eCamp\\Core\\Entity\\Organization' => array(
                'route_identifier_name' => 'organization_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.organization',
                'hydrator' => 'eCamp\\Core\\Hydrator\\OrganizationHydrator',
                'max_depth' => 2,
            ),
            'eCampApi\\V1\\Rest\\Organization\\OrganizationCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.organization',
                'is_collection' => true,
                'max_depth' => 2,
            ),
            'eCamp\\Core\\Entity\\Camp' => array(
                'route_identifier_name' => 'camp_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.camp',
                'hydrator' => 'eCamp\\Core\\Hydrator\\CampHydrator',
                'max_depth' => 2,
            ),
            'eCampApi\\V1\\Rest\\Camp\\CampCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.camp',
                'is_collection' => true,
                'max_depth' => 2,
            ),
            'eCamp\\Core\\Entity\\Period' => array(
                'route_identifier_name' => 'period_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.period',
                'hydrator' => 'eCamp\\Core\\Hydrator\\PeriodHydrator',
                'max_depth' => 2,
            ),
            'eCampApi\\V1\\Rest\\Period\\PeriodCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.period',
                'is_collection' => true,
                'max_depth' => 2,
            ),
            'eCamp\\Core\\Entity\\Day' => array(
                'route_identifier_name' => 'day_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.day',
                'hydrator' => 'eCamp\\Core\\Hydrator\\DayHydrator',
                'max_depth' => 2,
            ),
            'eCampApi\\V1\\Rest\\Day\\DayCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.day',
                'is_collection' => true,
                'max_depth' => 2,
            ),
            'eCamp\\Core\\Entity\\Event' => array(
                'route_identifier_name' => 'event_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event',
                'hydrator' => 'eCamp\\Core\\Hydrator\\EventHydrator',
                'max_depth' => 2,
            ),
            'eCampApi\\V1\\Rest\\Event\\EventCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event',
                'is_collection' => true,
                'max_depth' => 2,
            ),
            'eCamp\\Core\\Entity\\EventCategory' => array(
                'route_identifier_name' => 'event_category_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event-category',
                'hydrator' => 'eCamp\\Core\\Hydrator\\EventCategoryHydrator',
                'max_depth' => 2,
            ),
            'eCampApi\\V1\\Rest\\EventCategory\\EventCategoryCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event-category',
                'is_collection' => true,
                'max_depth' => 2,
            ),
            'eCamp\\Core\\Entity\\EventInstance' => array(
                'route_identifier_name' => 'event_instance_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event-instance',
                'hydrator' => 'eCamp\\Core\\Hydrator\\EventInstanceHydrator',
                'max_depth' => 2,
            ),
            'eCampApi\\V1\\Rest\\EventInstance\\EventInstanceCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.event-instance',
                'is_collection' => true,
                'max_depth' => 2,
            ),
            'eCamp\\Core\\Entity\\User' => array(
                'route_identifier_name' => 'user_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.user',
                'hydrator' => 'eCamp\\Core\\Hydrator\\UserHydrator',
                'max_depth' => 2,
            ),
            'eCampApi\\V1\\Rest\\User\\UserCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.user',
                'is_collection' => true,
                'max_depth' => 2,
            ),
            'eCamp\\Core\\Entity\\CampCollaboration' => array(
                'route_identifier_name' => 'camp_collaboration_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.camp-collaboration',
                'hydrator' => 'eCamp\\Core\\Hydrator\\CampCollaborationHydrator',
                'max_depth' => 2,
            ),
            'eCampApi\\V1\\Rest\\CampCollaboration\\CampCollaborationCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'e-camp-api.rest.doctrine.camp-collaboration',
                'is_collection' => true,
                'max_depth' => 2,
            ),
        ),
    ),
    'zf-content-validation' => array(
        'eCampApi\\V1\\Rest\\CampType\\Controller' => array(
            'input_filter' => 'eCampApi\\V1\\Rest\\CampType\\Validator',
        ),
        'eCampApi\\V1\\Rest\\EventType\\Controller' => array(
            'input_filter' => 'eCampApi\\V1\\Rest\\EventType\\Validator',
        ),
        'eCampApi\\V1\\Rest\\Organization\\Controller' => array(
            'input_filter' => 'eCampApi\\V1\\Rest\\Organization\\Validator',
        ),
        'eCampApi\\V1\\Rest\\Camp\\Controller' => array(
            'input_filter' => 'eCampApi\\V1\\Rest\\Camp\\Validator',
        ),
        'eCampApi\\V1\\Rest\\Period\\Controller' => array(
            'input_filter' => 'eCampApi\\V1\\Rest\\Period\\Validator',
        ),
        'eCampApi\\V1\\Rest\\Day\\Controller' => array(
            'input_filter' => 'eCampApi\\V1\\Rest\\Day\\Validator',
        ),
        'eCampApi\\V1\\Rest\\Event\\Controller' => array(
            'input_filter' => 'eCampApi\\V1\\Rest\\Event\\Validator',
        ),
        'eCampApi\\V1\\Rest\\EventCategory\\Controller' => array(
            'input_filter' => 'eCampApi\\V1\\Rest\\EventCategory\\Validator',
        ),
        'eCampApi\\V1\\Rest\\EventInstance\\Controller' => array(
            'input_filter' => 'eCampApi\\V1\\Rest\\EventInstance\\Validator',
        ),
        'eCampApi\\V1\\Rest\\User\\Controller' => array(
            'input_filter' => 'eCampApi\\V1\\Rest\\User\\Validator',
        ),
        'eCampApi\\V1\\Rest\\CampCollaboration\\Controller' => array(
            'input_filter' => 'eCampApi\\V1\\Rest\\CampCollaboration\\Validator',
        ),
        'eCampApi\\V1\\Rpc\\Login\\Controller' => array(
            'input_filter' => 'eCampApi\\V1\\Rpc\\Login\\Validator',
        ),
    ),
    'input_filter_specs' => array(
        'eCampApi\\V1\\Rest\\CampType\\Validator' => array(
            0 => array(
                'name' => 'name',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                ),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 64,
                        ),
                    ),
                ),
            ),
            1 => array(
                'name' => 'isJS',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
            ),
            2 => array(
                'name' => 'isCourse',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
            ),
            3 => array(
                'name' => 'jsonConfig',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
            ),
        ),
        'eCampApi\\V1\\Rest\\EventType\\Validator' => array(
            0 => array(
                'name' => 'name',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                ),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 64,
                        ),
                    ),
                ),
            ),
            1 => array(
                'name' => 'defaultColor',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                ),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 8,
                        ),
                    ),
                ),
            ),
            2 => array(
                'name' => 'defaultNumberingStyle',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                ),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 1,
                        ),
                    ),
                ),
            ),
        ),
        'eCampApi\\V1\\Rest\\Organization\\Validator' => array(
            0 => array(
                'name' => 'name',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                ),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 64,
                        ),
                    ),
                ),
            ),
        ),
        'eCampApi\\V1\\Rest\\Camp\\Validator' => array(
            0 => array(
                'name' => 'name',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                ),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 32,
                        ),
                    ),
                ),
            ),
            1 => array(
                'name' => 'title',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                ),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 64,
                        ),
                    ),
                ),
            ),
            2 => array(
                'name' => 'motto',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                ),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 128,
                        ),
                    ),
                ),
            ),
        ),
        'eCampApi\\V1\\Rest\\Period\\Validator' => array(
            0 => array(
                'name' => 'start',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
            ),
            1 => array(
                'name' => 'end',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
            ),
            2 => array(
                'name' => 'description',
                'required' => false,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                ),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 128,
                        ),
                    ),
                ),
            ),
        ),
        'eCampApi\\V1\\Rest\\Day\\Validator' => array(
            0 => array(
                'name' => 'dayOffset',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\Digits',
                    ),
                ),
                'validators' => array(),
            ),
        ),
        'eCampApi\\V1\\Rest\\Event\\Validator' => array(
            0 => array(
                'name' => 'title',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
            ),
        ),
        'eCampApi\\V1\\Rest\\EventCategory\\Validator' => array(
            0 => array(
                'name' => 'short',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                ),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 16,
                        ),
                    ),
                ),
            ),
            1 => array(
                'name' => 'name',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                ),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 64,
                        ),
                    ),
                ),
            ),
            2 => array(
                'name' => 'color',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                ),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 8,
                        ),
                    ),
                ),
            ),
            3 => array(
                'name' => 'numberingStyle',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                ),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 1,
                        ),
                    ),
                ),
            ),
        ),
        'eCampApi\\V1\\Rest\\EventInstance\\Validator' => array(
            0 => array(
                'name' => 'start',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\Digits',
                    ),
                ),
                'validators' => array(),
            ),
            1 => array(
                'name' => 'length',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\Digits',
                    ),
                ),
                'validators' => array(),
            ),
            2 => array(
                'name' => 'left',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
            ),
            3 => array(
                'name' => 'width',
                'required' => false,
                'filters' => array(),
                'validators' => array(),
            ),
        ),
        'eCampApi\\V1\\Rest\\User\\Validator' => array(
            0 => array(
                'name' => 'username',
                'required' => false,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                ),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 32,
                        ),
                    ),
                ),
            ),
            1 => array(
                'name' => 'state',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                ),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 16,
                        ),
                    ),
                ),
            ),
            2 => array(
                'name' => 'role',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                ),
                'validators' => array(
                    0 => array(
                        'name' => 'Zend\\Validator\\StringLength',
                        'options' => array(
                            'min' => 1,
                            'max' => 16,
                        ),
                    ),
                ),
            ),
        ),
        'eCampApi\\V1\\Rest\\CampCollaboration\\Validator' => array(
            0 => array(
                'name' => 'status',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                ),
                'validators' => array(),
            ),
            1 => array(
                'name' => 'role',
                'required' => true,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                ),
                'validators' => array(),
            ),
            2 => array(
                'name' => 'collaborationAcceptedBy',
                'required' => false,
                'filters' => array(
                    0 => array(
                        'name' => 'Zend\\Filter\\StringTrim',
                    ),
                    1 => array(
                        'name' => 'Zend\\Filter\\StripTags',
                    ),
                ),
                'validators' => array(),
            ),
        ),
        'eCampApi\\V1\\Rpc\\Login\\Validator' => array(),
    ),
    'controllers' => array(
        'factories' => array(
            'eCampApi\\V1\\Rpc\\Login\\Controller' => 'eCampApi\\V1\\Rpc\\Login\\LoginControllerFactory',
            'eCampApi\\V1\\Rpc\\Index\\Controller' => 'eCampApi\\V1\\Rpc\\Index\\IndexControllerFactory',
            'eCampApi\\V1\\Rpc\\Register\\Controller' => 'eCampApi\\V1\\Rpc\\Register\\RegisterControllerFactory',
        ),
    ),
    'zf-rpc' => array(
        'eCampApi\\V1\\Rpc\\Login\\Controller' => array(
            'service_name' => 'Login',
            'http_methods' => array(
                0 => 'GET',
                1 => 'POST',
            ),
            'route_name' => 'e-camp-api.rpc.login',
        ),
        'eCampApi\\V1\\Rpc\\Index\\Controller' => array(
            'service_name' => 'Index',
            'http_methods' => array(
                0 => 'GET',
            ),
            'route_name' => 'e-camp-api.rpc.index',
        ),
        'eCampApi\\V1\\Rpc\\Register\\Controller' => array(
            'service_name' => 'Register',
            'http_methods' => array(
                0 => 'POST',
            ),
            'route_name' => 'e-camp-api.rpc.register',
        ),
    ),
);
