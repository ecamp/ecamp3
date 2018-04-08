<?php

namespace eCamp\Core\Service;

use eCamp\Core\Hydrator\JobRespHydrator;
use eCamp\Core\Entity\JobResp;
use eCamp\Lib\Service\BaseService;

class JobRespService extends BaseService
{
    public function __construct(JobRespHydrator $jobRespHydrator)
    {
        parent::__construct($jobRespHydrator, JobResp::class);
    }
}
