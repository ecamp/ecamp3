<?php

namespace EcampApi\Resource\Resque\Job;

use EcampCore\Resource\BaseResourceListener;
use EcampLib\Job\Application;
use EcampLib\Job\JobResultInterface;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Resque\Job;
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

        if($e->getQueryParam('run') !== null){
            $this->performJob($job);
        }

        if ($e->getQueryParam('result') !== null) {
            $this->sendJobResult($job);
            throw new DomainException('Result not found', 404);
        }

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
        $job = $this->getResqueJobService()->Create($data->name, (array) $data);

        return new JobResource($job->enqueue());
    }

    private function sendJobResult(Job $job)
    {
        $instance = $job->getInstance();
        $instance->job = $job;

        if ($instance instanceof JobResultInterface) {
            $filename = $instance->getResult();

            if (file_exists($filename)) {

                //Get file type and set it as Content Type
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                header('Content-Type: ' . finfo_file($finfo, $filename));
                finfo_close($finfo);

                //Use Content-Disposition: attachment to specify the filename
                header('Content-Disposition: attachment; filename=' . basename($filename));

                //No cache
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');

                //Define file size
                header('Content-Length: ' . filesize($filename));

                ob_clean();
                flush();
                readfile($filename);
                exit;
            }
        }
    }

    private function performJob(Job $job){
        /** @var \Zend\Mvc\Application $app */
        $app = $this->getService('Application');
        Application::Set($app);

        $job->perform();
    }

}
