<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\JobRespHydrator;
use eCamp\Core\Entity\JobResp;

class JobRespService extends AbstractEntityService {
    public function __construct() {
        parent::__construct(
            JobResp::class,
            JobRespHydrator::class
        );
    }
}
