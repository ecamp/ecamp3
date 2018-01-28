<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Hydrator\JobHydrator;
use eCamp\Core\Entity\Job;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Service\BaseService;

class JobService extends BaseService
{
    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , JobHydrator $jobHydrator
    ) {
        parent::__construct($acl, $entityManager, $jobHydrator, Job::class);
    }
}
