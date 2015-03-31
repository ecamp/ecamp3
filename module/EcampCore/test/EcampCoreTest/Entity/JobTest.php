<?php

namespace EcampCoreTest\Entity;


use EcampCore\Entity\Camp;
use EcampCore\Entity\CampType;
use EcampCore\Entity\Job;

class JobTest extends \PHPUnit_Framework_TestCase
{

    private function createJob()
    {
        $campType = new CampType('CampType Name', 'CampType Type');
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

        $this->assertEquals('JobName', $job->getName());
        $this->assertEquals('CampName', $job->getCamp()->getName());

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $job->getJobResps());
    }

}