<?php

namespace EcampLib\Job;

abstract class AbstractJobBase
{
    public $job;
    public $args;
    public $queue;

    protected function __construct($defaultQueue = 'php-only')
    {
        $this->args = array();
        $this->queue = $defaultQueue;
    }

    public function enqueue($queue =  null)
    {
        $q = $queue ?: $this->queue;

        try {
            return \Resque::enqueue($q, get_class($this), $this->args);
        } catch(\Exception $ex) {
            $this->perform();
        }
    }

    abstract public function perform();

    protected function getToken()
    {
        if ($this->job instanceof \Resque_Job) {
            if (!empty($this->job->payload['id'])) {
                return $this->job->payload['id'];
            }
        }

        return null;
    }

    public function &get($name)
    {
        if (isset($this->args[$name])) {
            return $this->args[$name];
        }

        return null;
    }

    public function set($name, $value)
    {
        if (is_object($value)) {
            throw new \Exception("You can not store Objects in a Job-Context: ");
        }

        $this->args[$name] = $value;
    }

    public function &__get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function log($message)
    {
        if ($this->job instanceof \Resque_Job) {
            $this->job->worker->log($message);
        }
    }
}
