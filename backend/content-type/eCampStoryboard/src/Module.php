<?php

namespace eCamp\ContentType\Storyboard;

use eCamp\ContentType\Storyboard\Entity\Section;
use eCamp\Core\Acl\UserIsCollaborator;
use eCamp\Core\ContentType\ConfigFactory;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\User;
use eCamp\Lib\Acl\Acl;
use Laminas\Mvc\MvcEvent;
use Laminas\Permissions\Acl\AclInterface;

class Module {
    public function getConfig() {
        $config = ConfigFactory::createConfig('Storyboard', true, 'Section');

        array_push(
            $config['api-tools-rest']['eCamp\\ContentType\\Storyboard\\Controller\\SectionController']['collection_http_methods'],
            'PATCH'
        );

        return $config;
    }

    public function onBootstrap(MvcEvent $e): void {
        /** @var Acl $acl */
        $acl = $e->getApplication()->getServiceManager()->get(AclInterface::class);

        $acl->addResource(Section::class);

        $acl->allow(
            User::ROLE_USER,
            Section::class,
            [Acl::REST_PRIVILEGE_FETCH_ALL]
        );

        $acl->allow(
            User::ROLE_USER,
            Section::class,
            [Acl::REST_PRIVILEGE_FETCH],
            new UserIsCollaborator([CampCollaboration::ROLE_MEMBER, CampCollaboration::ROLE_MANAGER, CampCollaboration::ROLE_GUEST])
        );

        $acl->allow(
            User::ROLE_USER,
            Section::class,
            [
                Acl::REST_PRIVILEGE_CREATE,
                Acl::REST_PRIVILEGE_PATCH,
                Acl::REST_PRIVILEGE_UPDATE,
                Acl::REST_PRIVILEGE_DELETE,
            ],
            new UserIsCollaborator([CampCollaboration::ROLE_MEMBER, CampCollaboration::ROLE_MANAGER])
        );
    }
}
