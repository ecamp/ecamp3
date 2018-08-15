<?php
return [
    'factories' => [
        \eCamp\Core\EntityService\CampCollaborationService::class => \eCamp\Core\EntityServiceFactory\CampCollaborationServiceFactory::class,
        \eCamp\Core\EntityService\CampService::class => \eCamp\Core\EntityServiceFactory\CampServiceFactory::class,
        \eCamp\Core\EntityService\CampTypeService::class => \eCamp\Core\EntityServiceFactory\CampTypeServiceFactory::class,
        \eCamp\Core\EntityService\DayService::class => \eCamp\Core\EntityServiceFactory\DayServiceFactory::class,
        \eCamp\Core\EntityService\EventCategoryService::class => \eCamp\Core\EntityServiceFactory\EventCategoryServiceFactory::class,
        \eCamp\Core\EntityService\EventInstanceService::class => \eCamp\Core\EntityServiceFactory\EventInstanceServiceFactory::class,
        \eCamp\Core\EntityService\EventPluginService::class => \eCamp\Core\EntityServiceFactory\EventPluginServiceFactory::class,
        \eCamp\Core\EntityService\EventService::class => \eCamp\Core\EntityServiceFactory\EventServiceFactory::class,
        \eCamp\Core\EntityService\EventTemplateContainerService::class => \eCamp\Core\EntityServiceFactory\EventTemplateContainerServiceFactory::class,
        \eCamp\Core\EntityService\EventTemplateService::class => \eCamp\Core\EntityServiceFactory\EventTemplateServiceFactory::class,
        \eCamp\Core\EntityService\EventTypeFactoryService::class => \eCamp\Core\EntityServiceFactory\EventTypeFactoryServiceFactory::class,
        \eCamp\Core\EntityService\EventTypePluginService::class => \eCamp\Core\EntityServiceFactory\EventTypePluginServiceFactory::class,
        \eCamp\Core\EntityService\EventTypeService::class => \eCamp\Core\EntityServiceFactory\EventTypeServiceFactory::class,
        \eCamp\Core\EntityService\GroupMembershipService::class => \eCamp\Core\EntityServiceFactory\GroupMembershipServiceFactory::class,
        \eCamp\Core\EntityService\GroupService::class => \eCamp\Core\EntityServiceFactory\GroupServiceFactory::class,
        \eCamp\Core\EntityService\JobRespService::class => \eCamp\Core\EntityServiceFactory\JobRespServiceFactory::class,
        \eCamp\Core\EntityService\JobService::class => \eCamp\Core\EntityServiceFactory\JobServiceFactory::class,
        \eCamp\Core\EntityService\MediumService::class => \eCamp\Core\EntityServiceFactory\MediumServiceFactory::class,
        \eCamp\Core\EntityService\OrganizationService::class => \eCamp\Core\EntityServiceFactory\OrganizationServiceFactory::class,
        \eCamp\Core\EntityService\PeriodService::class => \eCamp\Core\EntityServiceFactory\PeriodServiceFactory::class,
        \eCamp\Core\EntityService\PluginService::class => \eCamp\Core\EntityServiceFactory\PluginServiceFactory::class,
        \eCamp\Core\EntityService\UserIdentityService::class => \eCamp\Core\EntityServiceFactory\UserIdentityServiceFactory::class,
        \eCamp\Core\EntityService\UserService::class => \eCamp\Core\EntityServiceFactory\UserServiceFactory::class,
    ]
];