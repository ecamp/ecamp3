<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait JobRespServiceTrait
{
    /** @var EntityService\JobRespService */
    private $jobRespService;

    public function setJobRespService(EntityService\JobRespService $jobRespService) {
        $this->jobRespService = $jobRespService;
    }

    public function getJobRespService() {
        return $this->jobRespService;
    }

}
