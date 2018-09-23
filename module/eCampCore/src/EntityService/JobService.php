<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\JobHydrator;
use eCamp\Core\Entity\Job;
use eCamp\Lib\Service\ServiceUtils;

class JobService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils) {
        parent::__construct(
            $serviceUtils,
            Job::class,
            JobHydrator::class
        );
    }
}
