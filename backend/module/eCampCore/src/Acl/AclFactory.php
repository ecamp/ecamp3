<?php

namespace eCamp\Core\Acl;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\Event;
use eCamp\Core\Entity\EventCategory;
use eCamp\Core\Entity\EventInstance;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Core\Entity\EventTemplate;
use eCamp\Core\Entity\EventTemplateContainer;
use eCamp\Core\Entity\EventType;
use eCamp\Core\Entity\EventTypeFactory;
use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Core\Entity\Group;
use eCamp\Core\Entity\GroupMembership;
use eCamp\Core\Entity\Organization;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\Plugin;
use eCamp\Core\Entity\User;
use eCamp\Core\Entity\UserIdentity;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\Guest;
use eCamp\Lib\Entity\BaseEntity;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AclFactory implements FactoryInterface {
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $acl = new Acl();

        //  Roles:
        // --------
        $acl->addRole(Guest::class);
        $acl->addRole(User::ROLE_GUEST, Guest::class);
        $acl->addRole(User::ROLE_USER, User::ROLE_GUEST);
        $acl->addRole(User::ROLE_ADMIN, User::ROLE_USER);


        //  Resources:
        // ------------
        $acl->addResource(BaseEntity::class);

        $acl->addResource(Organization::class, BaseEntity::class);
        $acl->addResource(Group::class, BaseEntity::class);
        $acl->addResource(GroupMembership::class, BaseEntity::class);

        $acl->addResource(CampType::class, BaseEntity::class);
        $acl->addResource(EventType::class, BaseEntity::class);
        $acl->addResource(EventTypeFactory::class, BaseEntity::class);

        $acl->addResource(Plugin::class, BaseEntity::class);
        $acl->addResource(EventTypePlugin::class, BaseEntity::class);

        $acl->addResource(Event::class, BaseEntity::class);
        $acl->addResource(EventCategory::class, BaseEntity::class);
        $acl->addResource(EventPlugin::class, BaseEntity::class);

        $acl->addResource(EventInstance::class, BaseEntity::class);

        $acl->addResource(EventTemplate::class, BaseEntity::class);
        $acl->addResource(EventTemplateContainer::class, BaseEntity::class);


        $acl->addResource(User::class, BaseEntity::class);
        $acl->addResource(UserIdentity::class, BaseEntity::class);


        $acl->addResource(Camp::class, BaseEntity::class);
        $acl->addResource(Period::class, BaseEntity::class);
        $acl->addResource(Day::class, BaseEntity::class);

        $acl->addResource(CampCollaboration::class, BaseEntity::class);


        //  ACL-Configuration:
        // --------------------
        $acl->allow(
            Guest::class,
            [
                Organization::class,
                Group::class,
                CampType::class,
                EventType::class,
                EventTemplate::class,
                EventTemplateContainer::class,
                EventTypeFactory::class,
                EventTypePlugin::class,
                Plugin::class,
            ], [
                Acl::REST_PRIVILEGE_FETCH,
                Acl::REST_PRIVILEGE_FETCH_ALL
            ]
        );
        $acl->allow(
            Guest::class,
            [
                UserIdentity::class,
                User::class
            ], [
                ACL::REST_PRIVILEGE_CREATE
            ]
        );


        $acl->allow(User::ROLE_USER, [User::class], [ACL::REST_PRIVILEGE_FETCH_ALL, ACL::REST_PRIVILEGE_FETCH]);
        $acl->allow(User::ROLE_USER, [Camp::class], [ACL::REST_PRIVILEGE_CREATE, ACL::REST_PRIVILEGE_FETCH_ALL]);
        $acl->allow(
            User::ROLE_USER,
            Camp::class,
            [
                Acl::REST_PRIVILEGE_FETCH,
                Acl::REST_PRIVILEGE_PATCH,
                Acl::REST_PRIVILEGE_UPDATE
            ],
            new UserIsCollaborator([CampCollaboration::ROLE_MEMBER, CampCollaboration::ROLE_MANAGER])
        );
        $acl->allow(
            User::ROLE_USER,
            Camp::class,
            Acl::REST_PRIVILEGE_DELETE,
            new UserIsCollaborator([CampCollaboration::ROLE_MANAGER])
        );
        $acl->allow(
            User::ROLE_USER,
            [
                CampCollaboration::class,
                Period::class,
                Day::class,
                Event::class,
                EventCategory::class,
                EventInstance::class
            ], [
                Acl::REST_PRIVILEGE_FETCH_ALL,
            ]
        );
        $acl->allow(
            User::ROLE_USER,
            [
                CampCollaboration::class,
                Period::class,
                Day::class,
                Event::class,
                EventCategory::class,
                EventInstance::class
            ], [
                Acl::REST_PRIVILEGE_CREATE,
                Acl::REST_PRIVILEGE_FETCH,
                Acl::REST_PRIVILEGE_DELETE,
                Acl::REST_PRIVILEGE_PATCH,
                Acl::REST_PRIVILEGE_UPDATE
            ],
            new UserIsCollaborator([CampCollaboration::ROLE_MEMBER, CampCollaboration::ROLE_MANAGER])
        );

        return $acl;
    }
}
