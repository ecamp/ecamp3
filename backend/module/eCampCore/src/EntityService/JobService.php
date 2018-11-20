<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\Job;
use eCamp\Core\Hydrator\JobHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class JobService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            $authenticationService,
            Job::class,
            JobHydrator::class
        );
    }
}
