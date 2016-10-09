<?php

namespace EcampLib\Job\Engine\Resque;

use EcampLib\Job\Engine\JobQueue as BaseJobQueue;
use EcampLib\Job\JobInterface;
use Resque\Job as ResqueJob;

class JobQueue extends BaseJobQueue
{
    /**
     * @var string
     */
    private $resqueQueueName;

    /**
     * @var \Resque\Queue
     */
    private $resqueQueue = null;

    /**
     * JobQueue constructor.
     * @param string $resqueQueueName
     */
    public function __construct($resqueQueueName = 'php')
    {
        $this->resqueQueueName = $resqueQueueName;
    }


    /**
     * @return string
     */
    public function getResqueQueueName()
    {
        return $this->resqueQueueName;
    }

    /**
     * @param string $resqueQueueName
     */
    public function setResqueQueueName($resqueQueueName)
    {
        $this->resqueQueueName = $resqueQueueName;
    }

    /**
     * @return \Resque\Queue
     */
    public function getResqueQueue()
    {
        if($this->resqueQueue == null){
            // @codeCoverageIgnoreStart
            $this->setResqueQueue(\Resque::queue());
            // @codeCoverageIgnoreEnd
        }
        return $this->resqueQueue;
    }

    /**
     * @param \Resque\Queue $resqueQueue
     */
    public function setResqueQueue($resqueQueue)
    {
        $this->resqueQueue = $resqueQueue;
    }


    /**
     * @param JobInterface $job
     */
    public function enqueue(JobInterface $job)
    {
        parent::enqueue($job);
    }

    /**
     * @return JobInterface
     */
    public function dequeue()
    {
        return ($this->count() > 0)
            ? parent::dequeue()
            : $this->resqueDequeue();
    }

    public function resqueEnqueue(JobInterface $job)
    {
        $this->getResqueQueue()->push(
            JobQueueItem::class,
            JobQueueItem::Data($job),
            $this->resqueQueueName
        );
    }

    public function resqueDequeue()
    {
        /** @var ResqueJob $resqueJob */
        $resqueJob = $this->getResqueQueue()->pop($this->resqueQueueName);

        return ($resqueJob instanceof ResqueJob)
            ? JobQueueItem::Create($resqueJob->getData(), $resqueJob)
            : null;
    }

    public function flush()
    {
        while($this->count() > 0){
            $this->resqueEnqueue( parent::dequeue() );
        }
    }
}
