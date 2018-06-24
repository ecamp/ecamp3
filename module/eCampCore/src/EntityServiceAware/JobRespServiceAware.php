<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface JobRespServiceAware {
    /**
     * @return EntityService\JobRespService
     */
    public function getJobRespService();

    /**
     * @param EntityService\JobRespService $jobRespService
     */
    public function setJobRespService(EntityService\JobRespService $jobRespService);
}
