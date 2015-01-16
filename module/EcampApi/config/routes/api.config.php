<?php
return array(
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
                    'controller'    => 'Resource\Camp'
                ),
            ),
            'may_terminate' => true,
            'child_routes' => array(

                'collaborators' => array(
                    'type' => 'Segment',
                    'may_terminate' => true,
                    'options' => array(
                        'route' => '/collaborators[/:collaborator]',
                        'defaults' => array(
                            'controller' => 'Resource\Camp\Collaboration'
                        )
                    ),
                    'child_routes' => array(
                        'action' => array(
                            'type' => 'Segment',
                            'options' => array(
                                'route'      => '/:action',
                                'defaults' => array(
                                    'controller' => 'Controller\CampCollaboration'
                                )
                            ),
                        ),
                    )
                ),

                'events' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route'      => '/events',
                        'defaults' => array(
                            'controller'    => 'Resource\Camp\Event'
                        ),
                    ),
                ),

                'periods' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route'      => '/periods',
                        'defaults' => array(
                            'controller'    => 'Resource\Camp\Period'
                        ),
                    ),
                ),

                'event_instances' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route'      => '/event_instances',
                        'defaults' => array(
                            'controller'    => 'Resource\Camp\EventInstance'
                        ),
                    ),
                ),

                'event_categories' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route'      => '/event_categories',
                        'defaults' => array(
                            'controller'    => 'Resource\Camp\EventCategory'
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
                    'controller'    => 'Resource\Collaboration'
                ),
            ),
            'may_terminate' => true,
            'child_routes' => array(

                'event_resps' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route'      => '/event_resps',
                        'defaults' => array(
                            'controller'    => 'Resource\Collaboration\EventResp'
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
                    'controller'    => 'Resource\Day'
                ),
            ),
            'may_terminate' => true,
            'child_routes' => array(

                'event_instances' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route'      => '/event_instances',
                        'defaults' => array(
                            'controller'    => 'Resource\Day\EventInstance'
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
                    'controller'    => 'Resource\Event'
                ),
            ),
            'may_terminate' => true,
            'child_routes' => array(

                'event_instances' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route'      => '/event_instances',
                        'defaults' => array(
                            'controller'    => 'Resource\Event\EventInstance'
                        ),
                    ),
                ),
                'event_resps' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route'      => '/event_resps',
                        'defaults' => array(
                            'controller'    => 'Resource\Event\EventResp'
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
                    'controller'    => 'Resource\EventCategory'
                ),
            ),
        ),

        'event_instances' => array(
            'type' => 'Segment',
            'options' => array(
                'route'      => '/event_instances[/:event_instance]',
                'defaults' => array(
                    'controller'    => 'Resource\EventInstance'
                ),
            ),
        ),

        'event_resps' => array(
            'type' => 'Segment',
            'options' => array(
                'route'      => '/event_resps[/:event_resp]',
                'defaults' => array(
                    'controller'    => 'Resource\EventResp'
                ),
            ),
        ),

        'periods' => array(
            'type' => 'Segment',
            'options' => array(
                'route'      => '/periods[/:period]',
                'defaults' => array(
                    'controller'    => 'Resource\Period'
                ),
            ),
            'may_terminate' => true,
            'child_routes' => array(

                'days' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route'      => '/days',
                        'defaults' => array(
                            'controller'    => 'Resource\Period\Day'
                        ),
                    ),
                ),

                'event_instances' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route'      => '/event_instances',
                        'defaults' => array(
                            'controller'    => 'Resource\Period\EventInstance'
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
                    'controller'    => 'Resource\User'
                ),
            ),
            'child_routes' => array(
                'camps' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route'      => '/camps',
                        'defaults' => array(
                            'controller'    => 'Resource\User\Camp'
                        ),
                    ),
                ),

                'collaborations' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route'      => '/collaborations',
                        'defaults' => array(
                            'controller'    => 'Resource\User\Collaboration'
                        ),
                    ),
                ),

                'event_resps' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route'      => '/event_resps',
                        'defaults' => array(
                            'controller'    => 'Resource\User\EventResp'
                        ),
                    ),
                ),

                'memberships' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route'      => '/memberships',
                        'defaults' => array(
                            'controller'    => 'Resource\User\Membership'
                        ),
                    ),
                ),
            ),
        ),

        'groups' => array(
            'type' => 'Segment',
            'may_terminate' => true,
            'options' => array(
                'route'      => '/groups[/:group]',
                'defaults' => array(
                    'controller'    => 'Resource\Group'
                ),
            ),
            'child_routes' => array(
                'camps' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route'      => '/camps',
                        'defaults' => array(
                            'controller'    => 'Resource\Group\Camp'
                        ),
                    ),
                ),

                'subgroups' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route'      => '/subgroups',
                        'defaults' => array(
                            'controller'    => 'Resource\Group\Subgroup'
                        ),
                    ),
                ),

                'members' => array(
                    'type' => 'Segment',
                    'may_terminate' => true,
                    'options' => array(
                        'route' => '/members[/:member]',
                        'defaults' => array(
                            'controller' => 'Resource\Group\Membership'
                        )
                    ),
                    'child_routes' => array(
                        'action' => array(
                            'type' => 'Segment',
                            'options' => array(
                                'route'      => '/:action',
                                'defaults' => array(
                                    'controller' => 'Controller\GroupMembership'
                                )
                            ),
                        ),
                    )
                )
            ),
        ),

        'memberships' => array(
            'type' => 'Segment',
            'may_terminate' => true,
            'options' => array(
                'route'      => '/memberships[/:membership]',
                'defaults' => array(
                    'controller'    => 'Resource\Membership'
                ),
            ),
        ),

        'images' => array(
            'type' => 'Segment',
            'may_terminate' => true,
            'options' => array(
                'route' => '/images/:image',
                'defaults' => array(
                    'controller' => 'Resource\Image'
                )
            ),
            'child_routes' => array(
                'default' => array(
                    'type' => 'Segment',
                    'may_terminater' => true,
                    'options' => array(
                        'route' => '/:action',
                        'defaults' => array(
                            'controller' => 'Controller\Image',
                            'action' => 'show'
                        )
                    )
                )
            )
        ),


        'search' => array(
            'type' => 'Literal',
            'may_terminate' => false,
            'options' => array(
                'route'      => '/search',
            ),

            'child_routes' => array(
                'user' => array(
                    'type' => 'Literal',
                    'may_terminate' => false,
                    'options' => array(
                        'route'      => '/user',
                        'defaults' => array(
                            'controller'    => 'Resource\Search\User'
                        ),
                    ),
                ),
            ),
        ),
    ),
);