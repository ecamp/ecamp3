<?php

namespace EcampApi\Resource\Resque\Job;

use EcampLib\Resource\BaseResourceListener;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\EventManagerInterface;

class JobResourceListener extends BaseResourceListener
{

    /**
     * @return \EcampCore\Service\ResqueJobService
     */
    protected function getResqueJobService()
    {
        return $this->getService('EcampCore\Service\ResqueJob');
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
        $this->listeners[] = $events->attach('create', array($this, 'onCreate'));
    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $job = $this->getResqueJobService()->Get($id);

        return new JobResource($job);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $jobResources = array();
        $jobs = $this->getResqueJobService()->GetAll();

        foreach ($jobs as $job) {
            $jobResources[] = new JobResource($job);
        }

        return $jobResources;
    }

    public function onCreate(ResourceEvent $e)
    {
        $data = $e->getParam('data');
        $job = $this->getResqueJobService()->Create($data->name, $data);

        return new JobResource($job->enqueue());
    }

}
