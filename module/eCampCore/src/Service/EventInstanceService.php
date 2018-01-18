<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Hydrator\EventInstanceHydrator;
use eCamp\Core\Entity\EventInstance;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Service\BaseService;

class EventInstanceService extends BaseService
{
    public function __construct(Acl $acl, EntityManager $entityManager) {
        parent::__construct($acl, $entityManager, EventInstance::class, EventInstanceHydrator::class);
    }

}
