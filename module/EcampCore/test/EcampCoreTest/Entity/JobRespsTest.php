<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\Camp;
use EcampCore\Entity\CampCollaboration;
use EcampCore\Entity\Job;
use EcampCore\Entity\JobResp;

class JobRespsTest extends \PHPUnit_Framework_TestCase
{

    private function createJobResp()
    {
        $day = DayTest::createDay();
        $period = $day->getPeriod();
        $camp = $day->getCamp();
        $job = new Job($camp);
        $job->setName('Job.Name');

        $user = $camp->getCreator();
        $campColl = CampCollaboration::createRequest($user, $camp);
        $campColl->acceptRequest($camp->getCreator());
        $campColl->PrePersist();

        $jobResp = new JobResp($day, $job, $campColl);
        $jobResp->prePersist();

        return $jobResp;
    }

    public function testJobResp()
    {
        $jobResp = $this->createJobResp();
        $job = $jobResp->getJob();
        $day = $jobResp->getDay();
        $camp = $jobResp->getCamp();

        $this->assertEquals("Camp.Name", $camp->getName());
        $this->assertEquals($camp, $jobResp->getPeriod()->getCamp());

        $this->assertEquals($camp, $jobResp->getCampCollaboration()->getCamp());
        $this->assertEquals("User.Username", $jobResp->getCampCollaboration()->getUser()->getUsername());
        $this->assertEquals("User.Username", $jobResp->getUser()->getUsername());

        $this->assertEquals("Job.Name", $jobResp->getJob()->getName());

        $this->assertContains($jobResp, $job->getJobResps());
        $this->assertContains($jobResp, $day->getJobResps());

        $this->assertTrue($job->isUserResp($day, $camp->getCreator()));

        $jobResp->preRemove();
        $this->assertNotContains($jobResp, $job->getJobResps());
        $this->assertNotContains($jobResp, $day->getJobResps());

    }

    /**
     * @expectedException \Exception
     */
    public function testDifferentCamps()
    {
        $day = DayTest::createDay();
        $camp = $day->getCamp();
        $job = JobTest::createJob();

        $user = $camp->getCreator();
        $campColl = CampCollaboration::createRequest($user, $camp);
        $campColl->acceptRequest($camp->getCreator());
        $campColl->PrePersist();

        new JobResp($day, $job, $campColl);
    }

}
