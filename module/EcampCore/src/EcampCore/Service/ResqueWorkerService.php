<?php

namespace EcampCore\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EcampLib\Service\ServiceBase;
use Resque\Host;
use Resque\Job;
use Resque\Redis;
use Resque\Worker;

class ResqueWorkerService extends ServiceBase
{

    /**
     * @param $id
     * @return Worker
     */
    public function Get($id)
    {
        $this->Cleanup();
        return Worker::fromId($id);
    }

    /**
     * @return Collection
     */
    public function GetAll()
    {
        $this->Cleanup();
        return new ArrayCollection(Worker::hostWorkers());
    }

    /**
     * @param $data
     * @return Worker|null
     */
    public function Create($data)
    {
        $pidFile = __DATA__ . '/tmp/' . uniqid() . '.pid';
        $options = array('--pid ' . $pidFile);
        $optionKeys = array(
            'queue', 'blocking', 'interval', 'timeout', 'memory',  'config',
            'include', 'host', 'port', 'schema', 'namespace', 'log',
        );

        foreach ($optionKeys as $key) {
            if (property_exists($data, $key)) {
                $options[] = '--' . $key . '=' . $data->{$key};
            }
        }

        $resqueBin = $this->getConfig()->get('resque')->get('bin');
        $command =  $resqueBin . ' worker:start ';
        $command .= implode($options, ' ') . ' ';
        $command .= /*'2>&1 &'; */ '> /dev/null 2> /dev/null &';

        $output = array();
        exec($command, $output);

        for ($i = 10; $i > 0; $i--) {
            if (file_exists($pidFile)) { break;  }
            sleep(1);
        }

        $worker = null;
        if (file_exists($pidFile)) {
            $host = new Host();
            $worker = $this->Get($host . ':' . file_get_contents($pidFile));
            unlink($pidFile);
        }

        return $worker;
    }

    public function Delete($id)
    {
        $worker = $this->Get($id);

        $command = 'kill -s term ' . $worker->getPid();
        $output = array();
        $result = array();

        exec($command, $output, $result);

        return true;
    }

    public function DeleteAll()
    {
        $ok = true;

        /** @var Worker $worker */
        foreach ($this->GetAll() as $worker) {
            $ok = $ok && posix_kill($worker->getPid(), SIGQUIT);
        }

        return $ok;
    }

    public function Cleanup()
    {
        $host = new Host();
        $host->cleanup();

        $worker = new Worker();
        $worker->cleanup();

        $host->cleanup();

        Job::cleanup();

        return true;
    }

}
