<?php

namespace eCamp\Core\ServiceManager;

use eCamp\Core\EntityService;
use eCamp\Core\EntityServiceAware;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;

final class EntityServiceInjector implements InitializerInterface {
    public function __invoke(ContainerInterface $container, $instance) {
        self::Inject($container, $instance);
    }

    public static function Inject(ContainerInterface $container, $instance) {
        if ($instance instanceof EntityServiceAware\CampCollaborationServiceAware) {
            $instance->setCampCollaborationService($container->get(EntityService\CampCollaborationService::class));
        }
        if ($instance instanceof EntityServiceAware\CampServiceAware) {
            $instance->setCampService($container->get(EntityService\CampService::class));
        }
        if ($instance instanceof EntityServiceAware\CampTypeServiceAware) {
            $instance->setCampTypeService($container->get(EntityService\CampTypeService::class));
        }
        if ($instance instanceof EntityServiceAware\DayServiceAware) {
            $instance->setDayService($container->get(EntityService\DayService::class));
        }
        if ($instance instanceof EntityServiceAware\EventCategoryServiceAware) {
            $instance->setEventCategoryService($container->get(EntityService\EventCategoryService::class));
        }
        if ($instance instanceof EntityServiceAware\EventInstanceServiceAware) {
            $instance->setEventInstanceService($container->get(EntityService\EventInstanceService::class));
        }
        if ($instance instanceof EntityServiceAware\EventPluginServiceAware) {
            $instance->setEventPluginService($container->get(EntityService\EventPluginService::class));
        }
        if ($instance instanceof EntityServiceAware\EventServiceAware) {
            $instance->setEventService($container->get(EntityService\EventService::class));
        }
        if ($instance instanceof EntityServiceAware\EventTemplateContainerServiceAware) {
            $instance->setEventTemplateContainerService($container->get(EntityService\EventTemplateContainerService::class));
        }
        if ($instance instanceof EntityServiceAware\EventTemplateServiceAware) {
            $instance->setEventTemplateService($container->get(EntityService\EventTemplateService::class));
        }
        if ($instance instanceof EntityServiceAware\EventTypeFactoryServiceAware) {
            $instance->setEventTypeFactoryService($container->get(EntityService\EventTypeFactoryService::class));
        }
        if ($instance instanceof EntityServiceAware\EventTypePluginServiceAware) {
            $instance->setEventTypePluginService($container->get(EntityService\EventTypePluginService::class));
        }
        if ($instance instanceof EntityServiceAware\EventTypeServiceAware) {
            $instance->setEventTypeService($container->get(EntityService\EventTypeService::class));
        }
        if ($instance instanceof EntityServiceAware\GroupMembershipServiceAware) {
            $instance->setGroupMembershipService($container->get(EntityService\GroupMembershipService::class));
        }
        if ($instance instanceof EntityServiceAware\GroupServiceAware) {
            $instance->setGroupService($container->get(EntityService\GroupService::class));
        }
        if ($instance instanceof EntityServiceAware\JobRespServiceAware) {
            $instance->setJobRespService($container->get(EntityService\JobRespService::class));
        }
        if ($instance instanceof EntityServiceAware\JobServiceAware) {
            $instance->setJobService($container->get(EntityService\JobService::class));
        }
        if ($instance instanceof EntityServiceAware\MediumServiceAware) {
            $instance->setMediumService($container->get(EntityService\MediumService::class));
        }
        if ($instance instanceof EntityServiceAware\OrganizationServiceAware) {
            $instance->setOrganizationService($container->get(EntityService\OrganizationService::class));
        }
        if ($instance instanceof EntityServiceAware\PeriodServiceAware) {
            $instance->setPeriodService($container->get(EntityService\PeriodService::class));
        }
        if ($instance instanceof EntityServiceAware\PluginServiceAware) {
            $instance->setPluginService($container->get(EntityService\PluginService::class));
        }
        if ($instance instanceof EntityServiceAware\UserIdentityServiceAware) {
            $instance->setUserIdentityService($container->get(EntityService\UserIdentityService::class));
        }
        if ($instance instanceof EntityServiceAware\UserServiceAware) {
            $instance->setUserService($container->get(EntityService\UserService::class));
        }
    }
}
