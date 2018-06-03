<?php

namespace eCamp\Core\EntityService;

use eCamp\Core\Hydrator\JobHydrator;
use eCamp\Core\Entity\Job;

class JobService extends AbstractEntityService
{
    public function __construct()
    {
        parent::__construct(
            Job::class,
            JobHydrator::class
        );
    }
}
