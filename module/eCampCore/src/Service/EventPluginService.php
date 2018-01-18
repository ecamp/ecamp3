<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Hydrator\EventPluginHydrator;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Service\BaseService;

class EventPluginService extends BaseService
{
    public function __construct(Acl $acl, EntityManager $entityManager) {
        parent::__construct($acl, $entityManager, EventPlugin::class, EventPluginHydrator::class);
    }

}
