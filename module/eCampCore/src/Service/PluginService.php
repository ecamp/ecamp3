<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Hydrator\PluginHydrator;
use eCamp\Core\Entity\Plugin;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Service\BaseService;

class PluginService extends BaseService
{
    public function __construct(Acl $acl, EntityManager $entityManager) {
        parent::__construct($acl, $entityManager, Plugin::class, PluginHydrator::class);
    }
}
