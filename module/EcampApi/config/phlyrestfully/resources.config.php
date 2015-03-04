<?php

return array(

    /**
     * Camp
     */
    'EcampApi\Resource\Camp' => array(
        'listener'                => 'EcampApi\Resource\Camp\CampResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get', 'put'),
        'route_name'              => 'api/camps',
        'identifier_name'		  => 'camp',
        'collection_query_whitelist' => array('user', 'past', 'collaborator', 'owning_only', 'owner', 'group', 'creator', 'search', 'mode'),
    ),

    'EcampApi\Resource\User\Camp' => array(
        'listener'                => 'EcampApi\Resource\Camp\CampResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/users/camps',
        'identifier_name'		  => 'camp',
        'collection_query_whitelist' => array('past', 'collaborator', 'owning_only', 'owner', 'group', 'creator', 'search', 'mode'),
    ),

    'EcampApi\Resource\Group\Camp' => array(
        'listener'                => 'EcampApi\Resource\Camp\CampResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/groups/camps',
        'identifier_name'		  => 'camp',
        'collection_query_whitelist' => array('user', 'past', 'collaborator', 'owning_only', 'owner', 'creator', 'search', 'mode'),
    ),

    /**
     * User
     */
    'EcampApi\Resource\User' => array(
        'listener'                => 'EcampApi\Resource\User\UserResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get', 'put'),
        'route_name'              => 'api/users',
        'identifier_name'		  => 'user',
    ),

    /**
     * Collaboration
     */
    'EcampApi\Resource\Collaboration' => array(
        'listener'                => 'EcampApi\Resource\Collaboration\CollaborationResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/collaborations',
        'identifier_name'		  => 'collaborator',
        'collection_query_whitelist' => array('user', 'camp'),
    ),

    'EcampApi\Resource\User\Collaboration' => array(
        'listener'                => 'EcampApi\Resource\Collaboration\CollaborationResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/users/collaborations',
        'identifier_name'		  => 'collaboration',
        'collection_query_whitelist' => array('camp'),
    ),

    'EcampApi\Resource\Camp\Collaboration' => array(
        'listener'                => 'EcampApi\Resource\Camp\CollaborationResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/camps/collaborators',
        'identifier_name'		  => 'collaborator',
//        'collection_query_whitelist' => array('user'),
    ),

    /**
     * Day
     */
    'EcampApi\Resource\Day' => array(
        'listener'                => 'EcampApi\Resource\Day\DayResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/days',
        'identifier_name'		  => 'day',
        'collection_query_whitelist' => array('period'),
    ),

    'EcampApi\Resource\Period\Day' => array(
        'listener'                => 'EcampApi\Resource\Day\DayResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/periods/days',
        'identifier_name'		  => 'day',
        'collection_query_whitelist' => array(),
    ),

    /**
     * Event
     */
    'EcampApi\Resource\Event' => array(
        'listener'                => 'EcampApi\Resource\Event\EventResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/events',
        'identifier_name'		  => 'event',
        'collection_query_whitelist' => array('camp'),
    ),

    'EcampApi\Resource\Camp\Event' => array(
        'listener'                => 'EcampApi\Resource\Event\EventResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/camps/events',
        'identifier_name'		  => 'event',
        'collection_query_whitelist' => array(),
    ),

    /**
     * Event Category
     */
    'EcampApi\Resource\EventCategory' => array(
        'listener'                => 'EcampApi\Resource\EventCategory\EventCategoryResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/event_categories',
        'identifier_name'		  => 'event_category',
        'collection_query_whitelist' => array(),
    ),

    'EcampApi\Resource\Camp\EventCategory' => array(
        'listener'                => 'EcampApi\Resource\EventCategory\EventCategoryResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/camps/event_categories',
        'identifier_name'		  => 'event_category',
        'collection_query_whitelist' => array(),
    ),

    /**
     * Event instance
     */
    'EcampApi\Resource\EventInstance' => array(
        'listener'                => 'EcampApi\Resource\EventInstance\EventInstanceResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get', 'put', 'delete'),
        'route_name'              => 'api/event_instances',
        'identifier_name'		  => 'event_instance',
        'collection_query_whitelist' => array(),
    ),

    'EcampApi\Resource\Event\EventInstance' => array(
        'listener'                => 'EcampApi\Resource\EventInstance\EventInstanceResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/events/event_instances',
        'identifier_name'		  => 'event_instance',
        'collection_query_whitelist' => array(),
    ),

    'EcampApi\Resource\Camp\EventInstance' => array(
        'listener'                => 'EcampApi\Resource\EventInstance\EventInstanceResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/camps/event_instances',
        'identifier_name'		  => 'event_instance',
        'collection_query_whitelist' => array(),
    ),

    'EcampApi\Resource\Period\EventInstance' => array(
        'listener'                => 'EcampApi\Resource\EventInstance\EventInstanceResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/periods/event_instances',
        'identifier_name'		  => 'event_instance',
        'collection_query_whitelist' => array(),
    ),

    'EcampApi\Resource\Day\EventInstance' => array(
        'listener'                => 'EcampApi\Resource\EventInstance\EventInstanceResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/days/event_instances',
        'identifier_name'		  => 'event_instance',
        'collection_query_whitelist' => array(),
    ),

    /**
     * Event Responsibles
     */
    'EcampApi\Resource\EventResp' => array(
        'listener'                => 'EcampApi\Resource\EventResp\EventRespResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/event_resps',
        'identifier_name'		  => 'event_resp',
        'collection_query_whitelist' => array('event','collaboration', 'user'),
    ),

    'EcampApi\Resource\Event\EventResp' => array(
        'listener'                => 'EcampApi\Resource\EventResp\EventRespResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/events/event_resps',
        'identifier_name'		  => 'event_resp',
        'collection_query_whitelist' => array(),
    ),

    'EcampApi\Resource\Collaboration\EventResp' => array(
        'listener'                => 'EcampApi\Resource\EventResp\EventRespResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/collaborations/event_resps',
        'identifier_name'		  => 'event_resp',
        'collection_query_whitelist' => array(),
    ),

    'EcampApi\Resource\User\EventResp' => array(
        'listener'                => 'EcampApi\Resource\EventResp\EventRespResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/users/event_resps',
        'identifier_name'		  => 'event_resp',
        'collection_query_whitelist' => array(),
    ),

    /**
     * Period
     */
    'EcampApi\Resource\Period' => array(
        'listener'                => 'EcampApi\Resource\Period\PeriodResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/periods',
        'identifier_name'		  => 'period',
        'collection_query_whitelist' => array('camp'),
    ),

    'EcampApi\Resource\Camp\Period' => array(
        'listener'                => 'EcampApi\Resource\Period\PeriodResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/camps/periods',
        'identifier_name'		  => 'period',
        'collection_query_whitelist' => array(),
    ),

    /**
     * Group
     */
    'EcampApi\Resource\Group' => array(
        'listener'                => 'EcampApi\Resource\Group\GroupResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get', 'put'),
        'route_name'              => 'api/groups',
        'identifier_name'		  => 'group',
        'collection_query_whitelist' => array(),
    ),

    'EcampApi\Resource\Group\Subgroup' => array(
        'listener'                => 'EcampApi\Resource\Group\GroupResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/groups/subgroups',
    ),

    'EcampApi\Resource\Group\Membership' => array(
        'listener'                => 'EcampApi\Resource\Group\MembershipResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/groups/members',
        'identifier_name'		  => 'member',
    ),

    /**
     * Membership
     */
    'EcampApi\Resource\Membership' => array(
        'listener'                => 'EcampApi\Resource\Membership\MembershipResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/memberships',
        'identifier_name'		  => 'membership',
        'collection_query_whitelist' => array('user','group'),
    ),

    'EcampApi\Resource\User\Membership' => array(
        'listener'                => 'EcampApi\Resource\Membership\MembershipResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/users/memberships',
        'collection_query_whitelist' => array('group'),
    ),

/*
    'EcampApi\Resource\Group\Membership' => array(
        'listener'                => 'EcampApi\Resource\Membership\MembershipResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/groups/memberships',
        'collection_query_whitelist' => array('user'),
    ),
*/

    'EcampApi\Resource\User\Image' => array(
        'listener'                => 'EcampApi\Resource\User\ImageResourceListener',
        'collection_http_options' => array(),
        'page_size'               => 1,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get', 'put', 'delete'),
        'route_name'              => 'api/users/image',
        'identifier_name'		  => 'user',
    ),

    'EcampApi\Resource\Image' => array(
        'listener'                => 'EcampApi\Resource\Image\ImageResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/images',
        'identifier_name'		  => 'image',
    ),

    'EcampApi\Resource\Search\User' => array(
        'listener'                => 'EcampApi\Resource\Search\UserResourceListener',
        'collection_http_options' => array('get'),
        'page_size'               => 3,
        'page_size_param'		  => 'limit',
        'resource_http_options'   => array('get'),
        'route_name'              => 'api/search/user',
        'collection_query_whitelist' => array('search'),
    ),
);
