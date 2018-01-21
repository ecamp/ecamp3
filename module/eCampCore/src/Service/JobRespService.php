<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Hydrator\JobRespHydrator;
use eCamp\Core\Entity\JobResp;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Service\BaseService;

class JobRespService extends BaseService
{
    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , JobRespHydrator $jobRespHydrator
    ) {
        parent::__construct
        ( $acl
        , $entityManager
        , $jobRespHydrator
        , JobResp::class
        );
    }
}
