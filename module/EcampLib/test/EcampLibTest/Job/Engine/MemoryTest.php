<?php

namespace EcampLibTest\Job\Engine;

use EcampLib\Job\Engine\Memory\JobQueue;

class MemoryTest extends \PHPUnit_Framework_TestCase
{

    public function testJobQueue()
    {
        $jobA = new DummyJob();
        $jobB = new DummyJob();

        $jobQueue = new JobQueue();
        $this->assertEquals(0, $jobQueue->count());

        $jobQueue->enqueue($jobA);
        $this->assertEquals(1, $jobQueue->count());

        $jobQueue->enqueue($jobB);
        $this->assertEquals(2, $jobQueue->count());

        $job = $jobQueue->dequeue();
        $this->assertEquals($jobA, $job);
        $this->assertEquals(1, $jobQueue->count());


        $this->assertFalse($jobB->isExecuted);
        $jobQueue->execute(new DummyApplication());
        $this->assertTrue($jobB->isExecuted);
        $this->assertEquals(0, $jobQueue->count());
    }

}
