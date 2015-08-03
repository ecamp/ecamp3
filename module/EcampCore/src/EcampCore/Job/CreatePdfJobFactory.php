<?php

namespace EcampCore\Job;

use EcampLib\Job\AbstractJobFactory;
use EcampLib\Job\JobInterface;

class CreatePdfJobFactory
    extends AbstractJobFactory
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
