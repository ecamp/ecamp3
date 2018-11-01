<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\JobRespHydrator;
use eCamp\Core\Entity\JobResp;
use eCamp\Lib\Service\ServiceUtils;

class JobRespService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils) {
        parent::__construct(
            $serviceUtils,
            JobResp::class,
            JobRespHydrator::class
        );
    }
}
