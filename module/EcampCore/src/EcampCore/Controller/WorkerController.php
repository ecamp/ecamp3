<?php

namespace EcampCore\Controller;

class WorkerController extends AbstractBaseController
{
    /**
     * @return \EcampCore\Service\ResqueJobService
     */
    private function getResqueJobService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\ResqueJob');
    }

    public function runAction()
    {
        /** @var \Resque\Job $job */
        $job = $this->getResqueJobService()->GetNextJob();

        while ($job) {
            /** @var \EcampLib\Job\AbstractJobBase $instance */
            $instance = $job->getInstance();
            $instance->setServiceLocator($this->getServiceLocator());
            $job->perform();

            // Next Job;/
            $job = $this->getResqueJobService()->GetNextJob();
        }
        die();
    }
}
