<?php

namespace eCamp\Core\Acl;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\Day;
use eCamp\Core\Entity\EventTemplate;
use eCamp\Core\Entity\EventTemplateContainer;
use eCamp\Core\Entity\EventType;
use eCamp\Core\Entity\EventTypeFactory;
use eCamp\Core\Entity\EventTypePlugin;
use eCamp\Core\Entity\Medium;
use eCamp\Core\Entity\Organization;
use eCamp\Core\Entity\Period;
use eCamp\Core\Entity\Plugin;
use eCamp\Core\Entity\User;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\Guest;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AclFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {

        $acl = new Acl();

        $acl->addRole(Guest::class);
        $acl->addRole(User::ROLE_GUEST, Guest::class);
        $acl->addRole(User::ROLE_USER, User::ROLE_GUEST);
        $acl->addRole(User::ROLE_ADMIN, User::ROLE_USER);

        $acl->addResource(Medium::class);
        $acl->addResource(Organization::class);

        $acl->addResource(CampType::class);
        $acl->addResource(EventType::class);
        $acl->addResource(EventTypeFactory::class);

        $acl->addResource(Plugin::class);
        $acl->addResource(EventTypePlugin::class);

        $acl->addResource(EventTemplate::class);
        $acl->addResource(EventTemplateContainer::class);


        $acl->addResource(User::class);

        $acl->addResource(Camp::class);
        $acl->addResource(Period::class);
        $acl->addResource(Day::class);


        $acl->allow(
            Guest::class,
            [
                Medium::class,
                Organization::class,
                CampType::class,
                EventType::class,
                EventTemplate::class,
                EventTemplateContainer::class,
                EventTypeFactory::class,
                EventTypePlugin::class,
                Plugin::class,
            ],
            [
                Acl::REST_PRIVILEGE_FETCH,
                Acl::REST_PRIVILEGE_FETCH_ALL
            ]
        );


        $acl->allow(Guest::class, User::class, ACL::REST_PRIVILEGE_CREATE);
        $acl->allow(User::ROLE_USER, User::class, [ACL::REST_PRIVILEGE_FETCH, ACL::REST_PRIVILEGE_FETCH_ALL]);

        $campAcl = new CampAcl();
        $acl->allow(User::ROLE_USER, Camp::class, Acl::REST_PRIVILEGE_FETCH_ALL, $campAcl);
        $acl->allow(User::ROLE_USER, Camp::class, Acl::REST_PRIVILEGE_FETCH, $campAcl);


        return $acl;
    }
}
