<?php
return array(
    'router' => array(
        'routes' => array(
            'api' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/api/v0',
                    'defaults' => array(
                        '__NAMESPACE__' => 'EcampApi'
                    )
                ),

                'may_terminate' => false,
                'child_routes' => array(
                    'login' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/login',
                            'defaults' => array(
                                'controller' => 'Controller\Login',
                                'action' => 'login'
                            )
                        )
                    ),

                    'logout' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/logout',
                            'defaults' => array(
                                'controller' => 'Controller\Login',
                                'action' => 'logout'
                            )
                        )
                    ),

                    'auth' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/auth[/:action]',
                            'defaults' => array(
                                'controller' => 'Controller\Auth'
                            )
                        )
                    ),

                    'camps' => array(
                        'type' => 'Segment',
                        'options' => array(
                             'route'      => '/camps[/:camp]',
                             'defaults' => array(
                                 'controller'    => 'Resource\Camp\ApiController'
                             ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(

                            'collaborations' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route'      => '/collaborations',
                                    'defaults' => array(
                                        'controller'    => 'Resource\Camp\Collaboration\ApiController'
                                    ),
                                ),
                            ),

                               'events' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route'      => '/events',
                                    'defaults' => array(
                                        'controller'    => 'Resource\Camp\Event\ApiController'
                                    ),
                                ),
                            ),

                            'periods' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route'      => '/periods',
                                    'defaults' => array(
                                        'controller'    => 'Resource\Camp\Period\ApiController'
                                    ),
                                ),
                            ),

                            'event_instances' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route'      => '/event_instances',
                                    'defaults' => array(
                                        'controller'    => 'Resource\Camp\EventInstance\ApiController'
                                    ),
                                ),
                            ),

                            'event_categories' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route'      => '/event_categories',
                                    'defaults' => array(
                                        'controller'    => 'Resource\Camp\EventCategory\ApiController'
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
                                'controller'    => 'Resource\Collaboration\ApiController'
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(

                            'event_resps' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route'      => '/event_resps',
                                    'defaults' => array(
                                        'controller'    => 'Resource\Collaboration\EventResp\ApiController'
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
                                'controller'    => 'Resource\Day\ApiController'
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(

                            'event_instances' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route'      => '/event_instances',
                                    'defaults' => array(
                                        'controller'    => 'Resource\Day\EventInstance\ApiController'
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
                                'controller'    => 'Resource\Event\ApiController'
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(

                            'event_instances' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route'      => '/event_instances',
                                    'defaults' => array(
                                        'controller'    => 'Resource\Event\EventInstance\ApiController'
                                    ),
                                ),
                            ),
                            'event_resps' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route'      => '/event_resps',
                                    'defaults' => array(
                                        'controller'    => 'Resource\Event\EventResp\ApiController'
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
                                'controller'    => 'Resource\EventCategory\ApiController'
                            ),
                        ),
                    ),

                    'event_instances' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'      => '/event_instances[/:event_instance]',
                            'defaults' => array(
                                'controller'    => 'Resource\EventInstance\ApiController'
                            ),
                        ),
                    ),

                    'event_resps' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'      => '/event_resps[/:event_resp]',
                            'defaults' => array(
                                'controller'    => 'Resource\EventResp\ApiController'
                            ),
                        ),
                    ),

                    'periods' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route'      => '/periods[/:period]',
                            'defaults' => array(
                                'controller'    => 'Resource\Period\ApiController'
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(

                            'days' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route'      => '/days',
                                    'defaults' => array(
                                        'controller'    => 'Resource\Period\Day\ApiController'
                                    ),
                                ),
                            ),

                            'event_instances' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route'      => '/event_instances',
                                    'defaults' => array(
                                        'controller'    => 'Resource\Period\EventInstance\ApiController'
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
                                'controller'    => 'Resource\User\ApiController'
                            ),
                        ),
                        'child_routes' => array(
                            'camps' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route'      => '/camps',
                                    'defaults' => array(
                                        'controller'    => 'Resource\User\Camp\ApiController'
                                    ),
                                ),
                            ),

                            'collaborations' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route'      => '/collaborations',
                                    'defaults' => array(
                                        'controller'    => 'Resource\User\Collaboration\ApiController'
                                    ),
                                ),
                            ),

                            'event_resps' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route'      => '/event_resps',
                                    'defaults' => array(
                                        'controller'    => 'Resource\User\EventResp\ApiController'
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
                    'EcampApi\Resource\Camp\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\Camp\CampResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/camps',
                            'identifier_name'		  => 'camp',
                            'collection_query_whitelist' => array('user', 'past', 'collaborator', 'owning_only', 'owner', 'group', 'creator', 'search'),
                            'collection_name'	 	  => 'camps'
                    ),

                    'EcampApi\Resource\User\Camp\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\Camp\CampResourceListener',
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
                    'EcampApi\Resource\User\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\User\UserResourceListener',
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
                    'EcampApi\Resource\Collaboration\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\Collaboration\CollaborationResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/collaborations',
                            'identifier_name'		  => 'collaboration',
                            'collection_query_whitelist' => array('user', 'camp'),
                            'collection_name'		  => 'collaborations'
                    ),

                    'EcampApi\Resource\User\Collaboration\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\Collaboration\CollaborationResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/users/collaborations',
                            'identifier_name'		  => 'collaboration',
                            'collection_query_whitelist' => array('camp'),
                            'collection_name'		  => 'collaborations'
                    ),

                    'EcampApi\Resource\Camp\Collaboration\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\Collaboration\CollaborationResourceListener',
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
                    'EcampApi\Resource\Day\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\Day\DayResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/days',
                            'identifier_name'		  => 'day',
                            'collection_query_whitelist' => array('period'),
                            'collection_name'		  => 'days'
                    ),

                    'EcampApi\Resource\Period\Day\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\Day\DayResourceListener',
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
                    'EcampApi\Resource\Event\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\Event\EventResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/events',
                            'identifier_name'		  => 'event',
                            'collection_query_whitelist' => array('camp'),
                            'collection_name'		  => 'events'
                    ),

                    'EcampApi\Resource\Camp\Event\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\Event\EventResourceListener',
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
                    'EcampApi\Resource\EventCategory\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\EventCategory\EventCategoryResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/event_categories',
                            'identifier_name'		  => 'event_category',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'event_categories'
                    ),
                    'EcampApi\Resource\Camp\EventCategory\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\EventCategory\EventCategoryResourceListener',
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
                    'EcampApi\Resource\EventInstance\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\EventInstance\EventInstanceResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/event_instances',
                            'identifier_name'		  => 'event_instance',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'event_instances'
                    ),

                    'EcampApi\Resource\Event\EventInstance\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\EventInstance\EventInstanceResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/events/event_instances',
                            'identifier_name'		  => 'event_instance',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'event_instances'
                    ),

                    'EcampApi\Resource\Camp\EventInstance\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\EventInstance\EventInstanceResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/camps/event_instances',
                            'identifier_name'		  => 'event_instance',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'event_instances'
                    ),

                    'EcampApi\Resource\Period\EventInstance\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\EventInstance\EventInstanceResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/periods/event_instances',
                            'identifier_name'		  => 'event_instance',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'event_instances'
                    ),

                    'EcampApi\Resource\Day\EventInstance\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\EventInstance\EventInstanceResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/days/event_instances',
                            'identifier_name'		  => 'event_instance',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'event_instances'
                    ),

                    'EcampApi\Resource\EventInstance\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\EventInstance\EventInstanceResourceListener',
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
                    'EcampApi\Resource\EventResp\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\EventResp\EventRespResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/event_resps',
                            'identifier_name'		  => 'event_resp',
                            'collection_query_whitelist' => array('event','collaboration', 'user'),
                            'collection_name'		  => 'event_resps'
                    ),

                    'EcampApi\Resource\Event\EventResp\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\EventResp\EventRespResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/events/event_resps',
                            'identifier_name'		  => 'event_resp',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'event_resps'
                    ),

                    'EcampApi\Resource\Collaboration\EventResp\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\EventResp\EventRespResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/collaborations/event_resps',
                            'identifier_name'		  => 'event_resp',
                            'collection_query_whitelist' => array(),
                            'collection_name'		  => 'event_resps'
                    ),

                    'EcampApi\Resource\User\EventResp\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\EventResp\EventRespResourceListener',
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
                    'EcampApi\Resource\Period\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\Period\PeriodResourceListener',
                            'collection_http_options' => array('get'),
                            'page_size'               => 3,
                            'page_size_param'		  => 'limit',
                            'resource_http_options'   => array('get'),
                            'route_name'              => 'api/periods',
                            'identifier_name'		  => 'period',
                            'collection_query_whitelist' => array('camp'),
                            'collection_name'		  => 'periods'
                    ),

                    'EcampApi\Resource\Camp\Period\ApiController' => array(
                            'listener'                => 'EcampApi\Resource\Period\PeriodResourceListener',
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
