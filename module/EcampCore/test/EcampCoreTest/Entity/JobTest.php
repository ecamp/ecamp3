<?php

namespace EcampCoreTest\Entity;

use EcampCore\Entity\Camp;
use EcampCore\Entity\CampType;
use EcampCore\Entity\Job;

class JobTest extends \PHPUnit_Framework_TestCase
{

    public static function createJob()
    {
        $campType = CampTypeTest::createCampType(true, CampType::ORGANIZATION_PBS, true);

        $camp = new Camp();
        $camp->setName('CampName');
        $camp->setCampType($campType);

        $job = new Job($camp);
        $job->setName('JobName');

        return $job;
    }

    public function testJob()
    {
        $job = $this->createJob();
        $camp = $job->getCamp();

        $this->assertEquals('JobName', $job->getName());
        $this->assertEquals('CampName', $camp->getName());

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $job->getJobResps());

        $job->prePersist();
        $this->assertContains($job, $camp->getJobs());

        $job->preRemove();
        $this->assertNotContains($job, $camp->getJobs());
    }

}
