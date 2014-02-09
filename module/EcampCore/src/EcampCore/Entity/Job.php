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
 * @ORM\Entity(repositoryClass="EcampCore\Repository\JobRepository")
 * @ORM\Table(name="jobs")
 * @ORM\HasLifecycleCallbacks
 */
class Job
    extends BaseEntity
{

    /**
     * @var Camp
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false)
     */
    private $camp;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="JobResp", mappedBy="job")
     */
    protected $jobResps;

    public function __construct(Camp $camp, $name)
    {
        parent::__construct();

        $this->camp = $camp;
        $this->name = $name;

        $this->jobResps = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return Camp
     */
    public function getCamp()
    {
        return $this->camp;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getJobResps()
    {
        return $this->jobResps;
    }

    /**
     * @param  User    $user
     * @return boolean
     */
    public function isUserResp(Day $day, User $user)
    {
        $filter = function($key, JobResp $jobResp) use ($user, $day) {
            return $jobResp->getUser() == $user && $jobResp->getDay() == $day;
        };

        return $this->jobResps->exists($filter);
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        parent::PrePersist();

        $this->camp->addToList('jobs', $this);
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemove()
    {
        $this->camp->removeFromList('jobs', $this);
    }

}
