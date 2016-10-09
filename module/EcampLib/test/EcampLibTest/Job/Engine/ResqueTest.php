<?php

namespace EcampLibTest\Job\Engine;

use EcampLib\Job\Engine\Application;
use EcampLib\Job\Engine\Resque\JobQueue;
use EcampLib\Job\Engine\Resque\JobQueueItem;

class ResqueTest extends \PHPUnit_Framework_TestCase
{

    public function testJobQueue()
    {
        /** @var \Resque\Queue $resqueQueue */
        $resqueQueue = new DummyResqueQueue();

        $jobA = new DummyJob();
        $jobB = new DummyJob();

        $jobQueue = new JobQueue();
        $jobQueue->setResqueQueue($resqueQueue);
        $this->assertEquals(0, $jobQueue->count());

        $jobQueue->enqueue($jobA);
        $this->assertEquals(1, $jobQueue->count());

        $jobQueue->enqueue($jobB);
        $this->assertEquals(2, $jobQueue->count());

        $job = $jobQueue->dequeue();
        $this->assertEquals($jobA, $job);
        $this->assertEquals(1, $jobQueue->count());

        $jobQueue->flush();
        $this->assertEquals(0, $jobQueue->count());

        $job = $jobQueue->dequeue();
        $this->assertNotNull($job);

        $job = $jobQueue->dequeue();
        $this->assertNull($job);

    }

    public function testJobQueueItem()
    {
        Application::SetInstance(new DummyApplication());

        /** @var \Resque\Queue $resqueQueue */
        $resqueQueue = new DummyResqueQueue();

        $jobA = new DummyJob();
        $jobB = new DummyJob();

        $jobQueue = new JobQueue();
        $jobQueue->setResqueQueue($resqueQueue);

        $jobQueue->enqueue($jobA);
        $jobQueue->enqueue($jobB);

        $jobQueue->flush();


        /** @var \Resque\Job $resqueJob */
        $resqueJob = $resqueQueue->pop();

        /** @var DummyJob $job */
        $job = $resqueJob->perform();
        $this->assertTrue($job->isExecuted);



        $resqueJob = $resqueQueue->pop();

        /** @var DummyJob $job */
        $job = $resqueJob->perform();
        $this->assertTrue($job->isExecuted);
        
    }

}

class DummyResqueQueue
{
    private $jobs = array();

    public function push($job, array $data = null, $queue = null)
    {
        $id = DummyResqueJob::createId('php', $job, $data, 0);
        $resqueJob = new DummyResqueJob('php', $id, $job, $data);
        array_push($this->jobs, $resqueJob);
    }

    public function pop()
    {
        return array_shift($this->jobs);
    }
}

class DummyResqueJob extends \Resque\Job
{
    public function __construct($queue, $id, $class, array $data)
    {
        // parent::__construct($queue, $id, $class, $data);

        $this->queue = $queue;
        $this->id    = $id;
        $this->data  = $data;

        $this->class = $class;
        if (strpos($this->class, '@')) {
            list($this->class, $this->method) = explode('@', $this->class, 2);
        }

        // Remove any spaces or back slashes
        $this->class = trim($this->class, '\\ ');

        $this->payload = $this->createPayload();
    }

    public function queue()
    {
        return true;
    }

    public function perform()
    {
        $instance = $this->getInstance();

        if (method_exists($instance, 'setUp')) {
            $instance->setUp();
        }

        $result = call_user_func_array(array($instance, $this->method), array($this->data, $this));

        if (method_exists($instance, 'tearDown')) {
            $instance->tearDown();
        }

        return $result;
    }
}