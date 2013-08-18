<?php

namespace EcampCore\Acl;

use Zend\Config\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use EcampLib\Acl\Acl;
use EcampCore\Entity\User;

class AclFactory
    implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $acl = new Acl();

        $config = new Config($serviceLocator->get('config'));
        $roles = $config->ecamp->acl->roles->toArray();
        $resources = $config->ecamp->acl->resources->toArray();

        foreach ($roles as $role => $parent) {
            $acl->addRole($role, $parent);
        }
        foreach ($resources as $resource => $parent) {
            $acl->addResource($resource, $parent);
        }

        $acl->allow(User::ROLE_ADMIN, 'EcampCore\Entity\User');
        $acl->allow(User::ROLE_ADMIN, 'EcampCore\Entity\Camp');
        $acl->allow(User::ROLE_ADMIN, 'EcampCore\Entity\Group');

        $acl->allow(User::ROLE_ADMIN, 'EcampCore\Entity\User', 'support.start');
        $acl->allow(User::ROLE_GUEST, 'EcampCore\Entity\User', 'support.stop', new Assertion\AssertAdminInSupportModus());
        $acl->allow(User::ROLE_USER,  'EcampCore\Entity\User', 'support.stop', new Assertion\AssertAdminInSupportModus());
        $acl->allow(User::ROLE_ADMIN, 'EcampCore\Entity\User', 'support.stop', new Assertion\AssertAdminInSupportModus());

        $acl->allow(User::ROLE_GUEST, 'EcampCore\Entity\User', 'list', new Assertion\AssertUserList());
        $acl->allow(User::ROLE_USER,  'EcampCore\Entity\User', 'show', new Assertion\AssertUserShow());
        $acl->allow(User::ROLE_USER,  'EcampCore\Entity\User', 'visit', new Assertion\AssertUserVisit());
        $acl->allow(User::ROLE_USER,  'EcampCore\Entity\User', 'administrate', new Assertion\AssertUserAdministrate());

        $acl->allow(User::ROLE_USER, 'EcampCore\Entity\Camp', 'list', new Assertion\AssertCampList());
        $acl->allow(User::ROLE_USER, 'EcampCore\Entity\Camp', 'visit', new Assertion\AssertCampVisit());
        $acl->allow(User::ROLE_USER, 'EcampCore\Entity\Camp', 'contribute', new Assertion\AssertCampContribute());
        $acl->allow(User::ROLE_USER, 'EcampCore\Entity\Camp', 'configure', new Assertion\AssertCampConfigure());
        $acl->allow(User::ROLE_USER, 'EcampCore\Entity\Camp', 'administrate', new Assertion\AssertCampAdministrate());

        $acl->allow(User::ROLE_USER, 'EcampCore\Entity\Group', 'list', new Assertion\AssertGroupList());
        $acl->allow(User::ROLE_USER, 'EcampCore\Entity\Group', 'visit', new Assertion\AssertGroupVisit());
        $acl->allow(User::ROLE_USER, 'EcampCore\Entity\Group', 'contribute', new Assertion\AssertGroupContribute());
        $acl->allow(User::ROLE_USER, 'EcampCore\Entity\Group', 'administrate', new Assertion\AssertGroupAdministrate());

        return $acl;
    }
}
