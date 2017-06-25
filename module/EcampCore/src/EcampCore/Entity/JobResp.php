<?php
/*
 * Copyright (C) 2011 Urban Suppiger
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace EcampCore\Entity;

use Doctrine\ORM\Mapping as ORM;

use EcampLib\Entity\BaseEntity;

/**
 * @ORM\Entity(repositoryClass="EcampCore\Repository\JobRespRepository")
 * @ORM\Table(name="job_resps")
 * @ORM\HasLifecycleCallbacks
 */
class JobResp
    extends BaseEntity
{

    /**
     * @var Day
     * @ORM\ManyToOne(targetEntity="Day")
     * @ORM\JoinColumn(nullable=false)
     */
    private $day;

    /**
     * @var Job
     * @ORM\ManyToOne(targetEntity="Job")
     * @ORM\JoinColumn(nullable=false)
     */
    private $job;

    /**
     * @var CampCollaboration
     * @ORM\ManyToOne(targetEntity="CampCollaboration")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campCollaboration;

    public function __construct(Day $day, Job $job, CampCollaboration $campCollaboration)
    {
        parent::__construct();

        if(	$day->getCamp() != $campCollaboration->getCamp()
        ||	$job->getCamp() != $campCollaboration->getCamp()
        ||	$job->getCamp() != $day->getCamp()
        ){
            throw new \Exception("Day and CampCollaboration are not from the same Camp!");
        }

        $this->day = $day;
        $this->job = $job;
        $this->campCollaboration = $campCollaboration;
    }

    /**
     * @return \EcampCore\Entity\Day
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @return \EcampCore\Entity\Period
     */
    public function getPeriod()
    {
        return $this->day->getPeriod();
    }

    /**
     * @return \EcampCore\Entity\Camp
     */
    public function getCamp()
    {
        return $this->day->getCamp();
    }

    /**
     * @return \EcampCore\Entity\Job
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @return \EcampCore\Entity\CampCollaboration
     */
    public function getCampCollaboration()
    {
        return $this->campCollaboration;
    }

    /**
     * @return \EcampCore\Entity\User
     */
    public function getUser()
    {
        return $this->campCollaboration->getUser();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        parent::PrePersist();

        $this->day->addToList('jobResps', $this);
        $this->job->addToList('jobResps', $this);
        $this->campCollaboration->addToList('jobResps', $this);
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemove()
    {
        $this->day->removeFromList('jobResps', $this);
        $this->job->removeFromList('jobResps', $this);
        $this->campCollaboration->removeFromList('jobResps', $this);
    }

}
