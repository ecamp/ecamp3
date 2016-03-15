<?php

namespace EcampApi\Resource\Resque\Worker;

use EcampLib\Resource\BaseResourceListener;
use PhlyRestfully\Exception\CreationException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\EventManagerInterface;

class WorkerResourceListener extends BaseResourceListener
{

    /**
     * @return \EcampCore\Service\ResqueWorkerService
     */
    protected function getResqueWorkerService()
    {
        return $this->getService('EcampCore\Service\ResqueWorker');
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
        $this->listeners[] = $events->attach('create', array($this, 'onCreate'));
        $this->listeners[] = $events->attach('delete', array($this, 'onDelete'));
        $this->listeners[] = $events->attach('deleteList', array($this, 'onDeleteList'));
    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $worker = $this->getResqueWorkerService()->Get($id);

        return new WorkerResource($worker);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $workerResources = array();
        $workers = $this->getResqueWorkerService()->GetAll();

        foreach ($workers as $worker) {
            $workerResources[] = new WorkerResource($worker);
        }

        return $workerResources;
    }

    public function onCreate(ResourceEvent $e)
    {
        $data = $e->getParam('data');
        $worker = $this->getResqueWorkerService()->Create($data);

        if ($worker != null) {
            return new WorkerResource($worker);
        } else {
            throw new CreationException();
        }
    }

    public function onDelete(ResourceEvent $e)
    {
        $id = $e->getParam('id');

        return $this->getResqueWorkerService()->Delete($id);
    }

    public function onDeleteList(ResourceEvent $e)
    {
        return $this->getResqueWorkerService()->DeleteAll();
    }
}
