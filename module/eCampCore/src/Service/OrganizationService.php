<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Hydrator\OrganizationHydrator;
use eCamp\Core\Entity\Organization;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Service\BaseService;

class OrganizationService extends BaseService
{
    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , OrganizationHydrator $organizationHydrator
    ) {
        parent::__construct($acl, $entityManager, $organizationHydrator, Organization::class);
    }
}
