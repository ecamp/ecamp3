<?php

namespace EcampCore\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EcampLib\Service\ServiceBase;
use Resque\Host;
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
        return Worker::fromId($id);
    }

    /**
     * @return Collection
     */
    public function GetAll()
    {
        $workerIds = Redis::instance()->smembers('resque:workers');
        $workers = array_map(array($this, 'Get'), $workerIds);

        return new ArrayCollection($workers);
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
        $command .= '> /dev/null 2> /dev/null &';

        exec($command);

        for ($i = 10; $i > 0; $i--) {
            if (file_exists($pidFile)) { break;  }
            sleep(1);
        }

        if (file_exists($pidFile)) {
            $host = new Host();

            return $this->Get($host . ':' . file_get_contents($pidFile));
        }

        return null;
    }

    public function Delete($id)
    {
        $worker = $this->Get($id);

        return posix_kill($worker->getPid(), SIGQUIT);

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

}
