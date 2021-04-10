<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\Job;
use eCamp\Core\Hydrator\JobHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class JobService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            Job::class,
            null,
            JobHydrator::class,
            $authenticationService
        );
    }
}
