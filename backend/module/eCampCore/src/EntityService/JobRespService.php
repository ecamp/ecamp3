<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Auth\AuthService;
use eCamp\Core\Hydrator\JobRespHydrator;
use eCamp\Core\Entity\JobResp;
use eCamp\Lib\Service\ServiceUtils;

class JobRespService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthService $authService) {
        parent::__construct(
            $serviceUtils,
            $authService,
            JobResp::class,
            JobRespHydrator::class
        );
    }
}
