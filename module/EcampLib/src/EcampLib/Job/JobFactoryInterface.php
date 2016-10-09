<?php

namespace EcampLib\Job;

use Zend\ServiceManager\FactoryInterface;

interface JobFactoryInterface extends FactoryInterface
{
    /**
     * @param  array        $options
     * @return JobInterface
     */
    public function create($options = null);
}
