<?php

namespace EcampCore\Job;

use EcampLib\Job\JobFactoryInterface;
use EcampLib\Job\JobInterface;

class CreatePdfJobFactory implements JobFactoryInterface
{
    /**
     * @param  array        $options
     * @return JobInterface
     */
    public function create($options = null)
    {
        return new CreatePdfJob($options['pages']);
    }
}
