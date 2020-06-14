<?php

namespace eCamp\ContentType\Storyboard;

use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\Guest;
use Laminas\Mvc\MvcEvent;
use eCamp\Core\ContentType\ConfigFactory;
use Laminas\Permissions\Acl\AclInterface;
use eCamp\ContentType\Storyboard\Entity\Section;

class Module {
    public function getConfig() {
        $config = ConfigFactory::createConfig("Storyboard", true, "Section");
        return $config;
    }

    public function onBootstrap(MvcEvent $e) {
        /** @var Acl $acl */
        $acl = $e->getApplication()->getServiceManager()->get(AclInterface::class);

        $acl->addResource(Section::class);

        $acl->allow(
            Guest::class,
            Section::class,
            [
                Acl::REST_PRIVILEGE_FETCH,
                Acl::REST_PRIVILEGE_FETCH_ALL,
                Acl::REST_PRIVILEGE_CREATE,
                Acl::REST_PRIVILEGE_PATCH,
                Acl::REST_PRIVILEGE_UPDATE,
                Acl::REST_PRIVILEGE_DELETE,
            ]
        );
    }
}
