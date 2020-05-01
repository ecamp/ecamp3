<?php

namespace eCamp\Plugin\Textarea;

use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\Guest;
use eCamp\Plugin\Textarea\Entity\Textarea;
use Zend\Mvc\MvcEvent;
use Zend\Permissions\Acl\AclInterface;

class Module {
    public function getConfig() {
        return include __DIR__.'/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e) {
        /** @var Acl $acl */
        $acl = $e->getApplication()->getServiceManager()->get(AclInterface::class);

        $acl->addResource(Textarea::class);

        $acl->allow(
            Guest::class,
            Textarea::class,
            [
                Acl::REST_PRIVILEGE_FETCH,
                Acl::REST_PRIVILEGE_FETCH_ALL,
                // Acl::REST_PRIVILEGE_CREATE, // // disallow posting directly. Single entities should always be created via EventPlugin.
                Acl::REST_PRIVILEGE_PATCH,
                Acl::REST_PRIVILEGE_UPDATE,
                // Acl::REST_PRIVILEGE_DELETE, // disallow deleting directly. Single entities should always be deleted via EventPlugin.
            ]
        );
    }
}
