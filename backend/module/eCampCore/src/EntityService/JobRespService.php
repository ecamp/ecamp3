<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Entity\JobResp;
use eCamp\Core\Hydrator\JobRespHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;

class JobRespService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            $authenticationService,
            JobResp::class,
            JobRespHydrator::class
        );
    }
}
