<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait JobServiceTrait
{
    /** @var EntityService\JobService */
    private $jobService;

    public function setJobService(EntityService\JobService $jobService) {
        $this->jobService = $jobService;
    }

    public function getJobService() {
        return $this->jobService;
    }

}
