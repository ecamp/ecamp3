<?php
return array(
    'router' => array(
        'routes' => array(
            'api' => array(
                                'type' => 'Literal',
                                'options' => array(
                                        'route' => '/api/v0'
                                ),
                                'may_terminate' => false,
                                'child_routes' => array(
                                        'login' => array(
                                               'type' => 'Literal',
                                            'options' => array(
                                                'route' => '/login',
                                                'defaults' => array(
                                                    'controller' => 'EcampApi\Controller\Login',
                                                    'action' => 'login'
                                                )
                                            )
                                        ),

                                        'logout' => array(
                                            'type' => 'Literal',
                                            'options' => array(
                                                'route' => '/logout',
                                                'defaults' => array(
                                                    'controller' => 'EcampApi\Controller\Login',
                                                    'action' => 'logout'
                                                )
                                            )
                                        ),

                                        'auth' => array(
                                            'type' => 'Segment',
                                            'options' => array(
                                                'route' => '/auth[/:action]',
                                                'defaults' => array(
                                                    'controller' => 'EcampApi\Controller\Auth'
                                                )
                                            )
                                        ),

                                        'camps' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                        'route'      => '/camps[/:camp]',
                                                        'defaults' => array(
                                                               'controller'    => 'EcampApi\Camp\ApiController'
                                                        ),
                                                ),
                                                'may_terminate' => true,
                                                'child_routes' => array(

                                                        'collaborations' => array(
                                                                'type' => 'Segment',
                                                                'options' => array(
                                                                        'route'      => '/collaborations',
                                                                        'defaults' => array(
                                                                                'controller'    => 'EcampApi\Camp\Collaboration\ApiController'
                                                                        ),
                                                                ),
                                                        ),

                                                        'events' => array(
                                                                'type' => 'Segment',
                                                                'options' => array(
                                                                        'route'      => '/events',
                                                                        'defaults' => array(
                                                                                'controller'    => 'EcampApi\Camp\Event\ApiController'
                                                                        ),
                                                                ),
                                                        ),

                                                        'periods' => array(
                                                                'type' => 'Segment',
                                                                'options' => array(
                                                                        'route'      => '/periods',
                                                                        'defaults' => array(
                                                                                'controller'    => 'EcampApi\Camp\Period\ApiController'
                                                                        ),
                                                                ),
                                                        ),

                                                        'event_instances' => array(
                                                                'type' => 'Segment',
                                                                'options' => array(
                                                                        'route'      => '/event_instances',
                                                                        'defaults' => array(
                                                                                'controller'    => 'EcampApi\Camp\EventInstance\ApiController'
                                                                        ),
                                                                ),
                                                        ),

                                                        'event_categories' => array(
                                                                'type' => 'Segment',
                                                                'options' => array(
                                                                        'route'      => '/event_categories',
                                                                        'defaults' => array(
                                                                                'controller'    => 'EcampApi\Camp\EventCategory\ApiController'
                                                                        ),
                                                                ),
                                                        ),

                                                )
                                        ),

                                        'collaborations' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                        'route'      => '/collaborations[/:collaboration]',
                                                        'defaults' => array(
                                                                'controller'    => 'EcampApi\Collaboration\ApiController'
                                                        ),
                                                ),
                                                'may_terminate' => true,
                                                'child_routes' => array(

                                                        'event_resps' => array(
                                                                'type' => 'Segment',
                                                                'options' => array(
                                                                        'route'      => '/event_resps',
                                                                        'defaults' => array(
                                                                                'controller'    => 'EcampApi\Collaboration\EventResp\ApiController'
                                                                        ),
                                                                ),
                                                        ),
                                                ),
                                        ),

                                        'days' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                        'route'      => '/days[/:day]',
                                                        'defaults' => array(
                                                                'controller'    => 'EcampApi\Day\ApiController'
                                                        ),
                                                ),
                                                'may_terminate' => true,
                                                'child_routes' => array(

                                                        'event_instances' => array(
                                                                'type' => 'Segment',
                                                                'options' => array(
                                                                        'route'      => '/event_instances',
                                                                        'defaults' => array(
                                                                                'controller'    => 'EcampApi\Day\EventInstance\ApiController'
                                                                        ),
                                                                ),
                                                        ),
                                                ),
                                        ),

                                        'events' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                        'route'      => '/events[/:event]',
                                                        'defaults' => array(
                                                                'controller'    => 'EcampApi\Event\ApiController'
                                                        ),
                                                ),
                                                'may_terminate' => true,
                                                'child_routes' => array(

                                                        'event_instances' => array(
                                                                'type' => 'Segment',
                                                                'options' => array(
                                                                        'route'      => '/event_instances',
                                                                        'defaults' => array(
                                                                                'controller'    => 'EcampApi\Event\EventInstance\ApiController'
                                                                        ),
                                                                ),
                                                        ),
                                                        'event_resps' => array(
                                                                'type' => 'Segment',
                                                                'options' => array(
                                                                        'route'      => '/event_resps',
                                                                        'defaults' => array(
                                                                                'controller'    => 'EcampApi\Event\EventResp\ApiController'
                                                                        ),
                                                                ),
                                                        ),
                                                ),
                                        ),

                                        'event_categories' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                        'route'      => '/event_categories[/:event_category]',
                                                        'defaults' => array(
                                                                'controller'    => 'EcampApi\EventCategory\ApiController'
                                                        ),
                                                ),
                                        ),

                                        'event_instances' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                        'route'      => '/event_instances[/:event_instance]',
                                                        'defaults' => array(
                                                                'controller'    => 'EcampApi\EventInstance\ApiController'
                                                        ),
                                                ),
                                        ),

                                        'event_resps' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                        'route'      => '/event_resps[/:event_resp]',
                                                        'defaults' => array(
                                                                'controller'    => 'EcampApi\EventResp\ApiController'
                                                        ),
                                                ),
                                        ),

                                        'periods' => array(
                                                'type' => 'Segment',
                                                'options' => array(
                                                        'route'      => '/periods[/:period]',
                                                        'defaults' => array(
                                                                'controller'    => 'EcampApi\Period\ApiController'
                                                        ),
                                                ),
                                                'may_terminate' => true,
                                                'child_routes' => array(

                                                        'days' => array(
                                                                'type' => 'Segment',
                                                                'options' => array(
                                                                        'route'      => '/days',
                                                                        'defaults' => array(
                                                                                'controller'    => 'EcampApi\Period\Day\ApiController'
                                                                        ),
                                                                ),
                                                        ),

                                                        'event_instances' => array(
                                                                'type' => 'Segment',
                                                                'options' => array(
                                                                        'route'      => '/event_instances',
                                                                        'defaults' => array(
                                                                                'controller'    => 'EcampApi\Period\EventInstance\ApiController'
                                                                        ),
                                                                ),
                                                        ),

                                                )
                                        ),

                                        'users' => array(
                                                'type' => 'Segment',
                                                'may_terminate' => true,
                                                'options' => array(
                                                        'route'      => '/users[/:user]',
                                                        'defaults' => array(
                                                                'controller'    => 'EcampApi\User\ApiController'
                                                        ),
                                                ),
                                                'child_routes' => array(
                                                        'camps' => array(
                                                                'type' => 'Segment',
                                                                'options' => array(
                                                                        'route'      => '/camps',
                                                                        'defaults' => array(
                                                                                'controller'    => 'EcampApi\User\Camp\ApiController'
                                                                        ),
                                                                ),
                                                        ),

                                                        'collaborations' => array(
                                                                'type' => 'Segment',
                                                                'options' => array(
                                                                        'route'      => '/collaborations',
                                                                        'defaults' => array(
                                                                                'controller'    => 'EcampApi\User\Collaboration\ApiController'
                                                                        ),
                                                                ),
                                                        ),

                                                        'event_resps' => array(
                                                                'type' => 'Segment',
                                                                'options' => array(
                                                                        'route'      => '/event_resps',
                                                                        'defaults' => array(
                                                                                'controller'    => 'EcampApi\User\EventResp\ApiController'
                                                                        ),
                                                                ),
                                                        ),
                                                )
                                        ),
                                ),
                        ),

        ),
    ),

    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),

    'phlyrestfully' => array(
            'resources' => array(

                    /**
                     * Camp
                     */
                    'EcampApi\Camp\ApiController' => array(
                            'listener'                => 'EcampApi\Camp\CampResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/camps',
                            'identifier_name'		  => 'camp',
                            'collection_query_whitelist' => array('user', 'past', 'collaborator', 'owning_only', 'owner', 'group', 'creator', 'search'),
                            'collection_name'	 	  => 'camps'
                    ),

                    'EcampApi\User\Camp\ApiController' => array(
                            'listener'                => 'EcampApi\Camp\CampResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/users/camps',
                            'identifier_name'		  => 'camp',
                            'collection_query_whitelist' => array('past', 'collaborator', 'owning_only', 'owner', 'group', 'creator', 'search'),
                            'collection_name'	 	  => 'camps'
                    ),

                    /**
                     * User
                     */
                    'EcampApi\User\ApiController' => array(
                            'listener'                => 'EcampApi\User\UserResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/users',
                            'identifier_name'		  => 'user',
                            'collection_name'	 	  => 'users'
                    ),

                    /**
                     * Collaboration
                     */
                    'EcampApi\Collaboration\ApiController' => array(
                            'listener'                => 'EcampApi\Collaboration\CollaborationResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/collaborations',
                            'identifier_name'		  => 'collaboration',
                            'collection_query_whitelist' => array('user', 'camp'),
                            'collection_name'		  => 'collaborations'
                    ),

                    'EcampApi\User\Collaboration\ApiController' => array(
                            'listener'                => 'EcampApi\Collaboration\CollaborationResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/users/collaborations',
                            'identifier_name'		  => 'collaboration',
                            'collection_query_whitelist' => array('camp'),
                            'collection_name'		  => 'collaborations'
                    ),

                    'EcampApi\Camp\Collaboration\ApiController' => array(
                            'listener'                => 'EcampApi\Collaboration\CollaborationResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/camps/collaborations',
                            'identifier_name'		  => 'collaboration',
                            'collection_query_whitelist' => array('user'),
                            'collection_name'		  => 'collaborations'
                    ),

                    /**
                     * Day
                     */
                    'EcampApi\Day\ApiController' => array(
                            'listener'                => 'EcampApi\Day\DayResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/days',
                            'identifier_name'		  => 'day',
                            'collection_query_whitelist' => array('period'),
                            'collection_name'		  => 'days'
                    ),

                    'EcampApi\Period\Day\ApiController' => array(
                            'listener'                => 'EcampApi\Day\DayResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/periods/days',
                            'identifier_name'		  => 'day',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'days'
                    ),

                    /**
                     * Event
                     */
                    'EcampApi\Event\ApiController' => array(
                            'listener'                => 'EcampApi\Event\EventResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/events',
                            'identifier_name'		  => 'event',
                            'collection_query_whitelist' => array('camp'),
                            'collection_name'		  => 'events'
                    ),

                    'EcampApi\Camp\Event\ApiController' => array(
                            'listener'                => 'EcampApi\Event\EventResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/camps/events',
                            'identifier_name'		  => 'event',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'events'
                    ),

                    /**
                     * Event Category
                     */
                    'EcampApi\EventCategory\ApiController' => array(
                            'listener'                => 'EcampApi\EventCategory\EventCategoryResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/event_categories',
                            'identifier_name'		  => 'event_category',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'event_categories'
                    ),
                    'EcampApi\Camp\EventCategory\ApiController' => array(
                            'listener'                => 'EcampApi\EventCategory\EventCategoryResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/camps/event_categories',
                            'identifier_name'		  => 'event_category',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'event_categories'
                    ),

                    /**
                     * Event instance
                     */
                    'EcampApi\EventInstance\ApiController' => array(
                            'listener'                => 'EcampApi\EventInstance\EventInstanceResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/event_instances',
                            'identifier_name'		  => 'event_instance',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'event_instances'
                    ),

                    'EcampApi\Event\EventInstance\ApiController' => array(
                            'listener'                => 'EcampApi\EventInstance\EventInstanceResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/events/event_instances',
                            'identifier_name'		  => 'event_instance',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'event_instances'
                    ),

                    'EcampApi\Camp\EventInstance\ApiController' => array(
                            'listener'                => 'EcampApi\EventInstance\EventInstanceResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/camps/event_instances',
                            'identifier_name'		  => 'event_instance',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'event_instances'
                    ),

                    'EcampApi\Period\EventInstance\ApiController' => array(
                            'listener'                => 'EcampApi\EventInstance\EventInstanceResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/periods/event_instances',
                            'identifier_name'		  => 'event_instance',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'event_instances'
                    ),

                    'EcampApi\Day\EventInstance\ApiController' => array(
                            'listener'                => 'EcampApi\EventInstance\EventInstanceResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/days/event_instances',
                            'identifier_name'		  => 'event_instance',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'event_instances'
                    ),

                    'EcampApi\EventInstance\ApiController' => array(
                            'listener'                => 'EcampApi\EventInstance\EventInstanceResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/event_instances',
                            'identifier_name'		  => 'event_instance',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'event_instances'
                    ),

                    /**
                     * Event Responsibles
                     */
                    'EcampApi\EventResp\ApiController' => array(
                            'listener'                => 'EcampApi\EventResp\EventRespResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/event_resps',
                            'identifier_name'		  => 'event_resp',
                            'collection_query_whitelist' => array('event','collaboration', 'user'),
                            'collection_name'		  => 'event_resps'
                    ),

                    'EcampApi\Event\EventResp\ApiController' => array(
                            'listener'                => 'EcampApi\EventResp\EventRespResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/events/event_resps',
                            'identifier_name'		  => 'event_resp',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'event_resps'
                    ),

                    'EcampApi\Collaboration\EventResp\ApiController' => array(
                            'listener'                => 'EcampApi\EventResp\EventRespResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/collaborations/event_resps',
                            'identifier_name'		  => 'event_resp',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'event_resps'
                    ),

                    'EcampApi\User\EventResp\ApiController' => array(
                            'listener'                => 'EcampApi\EventResp\EventRespResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/users/event_resps',
                            'identifier_name'		  => 'event_resp',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'event_resps'
                    ),

                    /**
                     * Period
                     */
                    'EcampApi\Period\ApiController' => array(
                            'listener'                => 'EcampApi\Period\PeriodResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/periods',
                            'identifier_name'		  => 'period',
                            'collection_query_whitelist' => array('camp'),
                            'collection_name'		  => 'periods'
                    ),

                    'EcampApi\Camp\Period\ApiController' => array(
                            'listener'                => 'EcampApi\Period\PeriodResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/camps/periods',
                            'identifier_name'		  => 'period',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'periods'
                    ),

            ),

    ),

    'doctrine' => array(
        'driver' => array(
            'ecamp_api_entities' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/EcampApi/Entity')
            ),

            'orm_default' => array(
                'drivers' => array(
                    'EcampApi\Entity' => 'ecamp_api_entities'
                )
            )
        ),
    ),

);
