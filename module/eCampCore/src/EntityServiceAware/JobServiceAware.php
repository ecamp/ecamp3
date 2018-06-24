<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface JobServiceAware {
    /**
     * @return EntityService\JobService
     */
    public function getJobService();

    /**
     * @param EntityService\JobService $jobService
     */
    public function setJobService(EntityService\JobService $jobService);
}
