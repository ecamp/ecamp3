<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Hydrator\GroupHydrator;
use eCamp\Core\Entity\Group;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Service\BaseService;

class GroupService extends BaseService
{
    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , GroupHydrator $groupHydrator
    ) {
        parent::__construct($acl, $entityManager, $groupHydrator, Group::class);
    }

}
