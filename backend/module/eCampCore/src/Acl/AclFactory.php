<?php

namespace eCamp\Core\Acl;

use eCamp\Core\Entity\AbstractContentNodeOwner;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ActivityResponsible;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\CategoryContentType;
use eCamp\Core\Entity\ContentNode;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\DayResponsible;
use eCamp\Core\Entity\Group;
use eCamp\Core\Entity\GroupMembership;
use eCamp\Core\Entity\MaterialItem;
use eCamp\Core\Entity\MaterialList;
use eCamp\Core\Entity\Organization;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\ScheduleEntry;
use eCamp\Core\Entity\User;
use eCamp\Core\Entity\UserIdentity;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\AclAssertion;
use eCamp\Lib\Acl\Guest;
use eCamp\Lib\Entity\BaseEntity;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Permissions\Acl\AclInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AclFactory implements FactoryInterface {
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AclInterface {
        $authenticationService = $container->get(AuthenticationService::class);

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

        $acl->addResource(AbstractContentNodeOwner::class, BaseEntity::class);
        $acl->addResource(Activity::class, BaseEntity::class);
        $acl->addResource(Category::class, BaseEntity::class);
        $acl->addResource(CategoryContentType::class, BaseEntity::class);
        $acl->addResource(ContentNode::class, BaseEntity::class);
        $acl->addResource(ContentType::class, BaseEntity::class);
        $acl->addResource(ActivityResponsible::class, BaseEntity::class);

        $acl->addResource(ScheduleEntry::class, BaseEntity::class);

        $acl->addResource(MaterialList::class, BaseEntity::class);
        $acl->addResource(MaterialItem::class, BaseEntity::class);

        $acl->addResource(User::class, BaseEntity::class);
        $acl->addResource(UserIdentity::class, BaseEntity::class);

        $acl->addResource(Camp::class, BaseEntity::class);
        $acl->addResource(Period::class, BaseEntity::class);
        $acl->addResource(Day::class, BaseEntity::class);

        $acl->addResource(CampCollaboration::class, BaseEntity::class);
        $acl->addResource(DayResponsible::class, BaseEntity::class);

        //  ACL-Configuration:
        // --------------------
        $acl->allow(
            Guest::class,
            [
                Organization::class,
                Group::class,
                ContentType::class,
            ],
            [
                Acl::REST_PRIVILEGE_FETCH,
                Acl::REST_PRIVILEGE_FETCH_ALL,
            ]
        );
        $acl->allow(
            Guest::class,
            [
                UserIdentity::class,
                User::class,
            ],
            [
                ACL::REST_PRIVILEGE_CREATE,
            ]
        );

        $acl->allow(User::ROLE_USER, [User::class], [ACL::REST_PRIVILEGE_FETCH_ALL, ACL::REST_PRIVILEGE_FETCH]);
        $acl->allow(
            User::ROLE_USER,
            User::class,
            [
                Acl::REST_PRIVILEGE_PATCH,
                Acl::REST_PRIVILEGE_UPDATE,
                Acl::REST_PRIVILEGE_DELETE,
            ],
            new UserIsAuthenticatedUser($authenticationService)
        );

        $acl->allow(User::ROLE_USER, [Camp::class], [ACL::REST_PRIVILEGE_CREATE, ACL::REST_PRIVILEGE_FETCH_ALL]);
        $acl->allow(
            User::ROLE_USER,
            [
                Camp::class,
                CampCollaboration::class,
                Period::class,
                Day::class,
                DayResponsible::class,
                AbstractContentNodeOwner::class,
                Activity::class,
                Category::class,
                CategoryContentType::class,
                ActivityResponsible::class,
                ScheduleEntry::class,
                ContentNode::class,
                MaterialList::class,
                MaterialItem::class,
            ],
            Acl::REST_PRIVILEGE_FETCH,
            AclAssertion::or(
                new CampIsPrototype(),
                new UserIsCollaborator([CampCollaboration::ROLE_MEMBER, CampCollaboration::ROLE_MANAGER, CampCollaboration::ROLE_GUEST])
            )
        );
        $acl->allow(
            User::ROLE_USER,
            Camp::class,
            [
                Acl::REST_PRIVILEGE_PATCH,
                Acl::REST_PRIVILEGE_UPDATE,
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
                DayResponsible::class,
                AbstractContentNodeOwner::class,
                Activity::class,
                Category::class,
                CategoryContentType::class,
                ActivityResponsible::class,
                ScheduleEntry::class,
                ContentNode::class,
                MaterialList::class,
                MaterialItem::class,
            ],
            Acl::REST_PRIVILEGE_FETCH_ALL,
        );
        $acl->allow(
            User::ROLE_USER,
            [
                CampCollaboration::class,
                Period::class,
                Day::class,
                DayResponsible::class,
                AbstractContentNodeOwner::class,
                Activity::class,
                Category::class,
                CategoryContentType::class,
                ActivityResponsible::class,
                ScheduleEntry::class,
                ContentNode::class,
                MaterialList::class,
                MaterialItem::class,
            ],
            [
                Acl::REST_PRIVILEGE_CREATE,
                Acl::REST_PRIVILEGE_DELETE,
                Acl::REST_PRIVILEGE_PATCH,
                Acl::REST_PRIVILEGE_UPDATE,
            ],
            new UserIsCollaborator([CampCollaboration::ROLE_MEMBER, CampCollaboration::ROLE_MANAGER])
        );

        return $acl;
    }
}
