<?php

namespace EcampLib\Job;

interface JobFactoryInterface
{
    /**
     * @param  array        $options
     * @return JobInterface
     */
    public function create($options = null);
}
