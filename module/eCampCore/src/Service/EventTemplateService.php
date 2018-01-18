<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Hydrator\EventTemplateHydrator;
use eCamp\Core\Entity\EventTemplate;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Service\BaseService;

class EventTemplateService extends BaseService
{
    public function __construct(Acl $acl, EntityManager $entityManager) {
        parent::__construct($acl, $entityManager, EventTemplate::class, EventTemplateHydrator::class);
    }
}
