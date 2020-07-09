<?php

namespace eCamp\ContentType\Richtext;

use eCamp\ContentType\Richtext\Entity\Richtext;
use eCamp\Core\ContentType\ConfigFactory;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\Guest;
use Laminas\Mvc\MvcEvent;
use Laminas\Permissions\Acl\AclInterface;

class Module {
    public function getConfig() {
        $config = ConfigFactory::createConfig('Richtext');
        $config['api-tools-content-validation'] = [
            'eCamp\\ContentType\\Richtext\\Controller\\RichtextController' => [
                'input_filter' => 'eCamp\\ContentType\\Richtext\\Entity\\Richtext\\Validator',
                'use_raw_data' => false,
            ],
        ];
        $config['input_filter_specs'] = [
            'eCamp\\ContentType\\Richtext\\Entity\\Richtext\\Validator' => [
                0 => [
                    'name' => 'text',
                    'required' => true,
                    'filters' => [
                        0 => ['name' => 'Laminas\\Filter\\StringTrim'],
                        1 => ['name' => \eCamp\Lib\InputFilter\HtmlPurify::class],
                    ],
                ],
            ],
        ];

        return $config;
    }

    public function onBootstrap(MvcEvent $e) {
        /** @var Acl $acl */
        $acl = $e->getApplication()->getServiceManager()->get(AclInterface::class);

        $acl->addResource(Richtext::class);

        $acl->allow(
            Guest::class,
            Richtext::class,
            [
                Acl::REST_PRIVILEGE_FETCH,
                Acl::REST_PRIVILEGE_FETCH_ALL,
                // Acl::REST_PRIVILEGE_CREATE,
                // disallow posting directly. Single entities should always be created via ActivityContent.
                Acl::REST_PRIVILEGE_PATCH,
                Acl::REST_PRIVILEGE_UPDATE,
                // Acl::REST_PRIVILEGE_DELETE,
                // disallow deleting directly. Single entities should always be deleted via ActivityContent.
            ]
        );
    }
}
