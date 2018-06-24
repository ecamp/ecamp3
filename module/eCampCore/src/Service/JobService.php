<?php

namespace eCamp\Core\Service;

use eCamp\Core\Hydrator\JobHydrator;
use eCamp\Core\Entity\Job;
use eCamp\Lib\Service\BaseService;

class JobService extends BaseService {
    public function __construct(JobHydrator $jobHydrator) {
        parent::__construct($jobHydrator, Job::class);
    }
}
