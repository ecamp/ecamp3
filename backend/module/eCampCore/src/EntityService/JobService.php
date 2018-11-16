<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Auth\AuthService;
use eCamp\Core\Hydrator\JobHydrator;
use eCamp\Core\Entity\Job;
use eCamp\Lib\Service\ServiceUtils;

class JobService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthService $authService) {
        parent::__construct(
            $serviceUtils,
            $authService,
            Job::class,
            JobHydrator::class
        );
    }
}
