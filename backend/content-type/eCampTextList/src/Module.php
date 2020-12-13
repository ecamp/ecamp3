<?php

namespace eCamp\ContentType\TextList;

use eCamp\ContentType\SingleText\Entity\TextListItem;
use eCamp\Core\Acl\UserIsCollaborator;
use eCamp\Core\ContentType\ConfigFactory;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\User;
use eCamp\Lib\Acl\Acl;
use Laminas\Mvc\MvcEvent;
use Laminas\Permissions\Acl\AclInterface;

class Module {
    public function getConfig() {
        $config = ConfigFactory::createConfig('TextList', true, 'ListItem');

        array_push(
            $config['api-tools-rest']['eCamp\\ContentType\\TextList\\Controller\\TextListItemController']['collection_http_methods'],
            'PATCH'
        );

        return $config;
    }

    public function onBootstrap(MvcEvent $e) {
        /** @var Acl $acl */
        $acl = $e->getApplication()->getServiceManager()->get(AclInterface::class);

        $acl->addResource(TextListItem::class);

        $acl->allow(
            User::ROLE_USER,
            TextListItem::class,
            [Acl::REST_PRIVILEGE_FETCH_ALL]
        );
        $acl->allow(
            User::ROLE_USER,
            TextListItem::class,
            [
                Acl::REST_PRIVILEGE_CREATE,
                Acl::REST_PRIVILEGE_FETCH,
                Acl::REST_PRIVILEGE_PATCH,
                Acl::REST_PRIVILEGE_UPDATE,
                Acl::REST_PRIVILEGE_DELETE,
            ],
            new UserIsCollaborator([CampCollaboration::ROLE_MEMBER, CampCollaboration::ROLE_MANAGER])
        );
    }
}
