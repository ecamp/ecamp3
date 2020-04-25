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

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="jobs")
 */
class Job extends BaseEntity implements BelongsToCampInterface {
    public function __construct() {
        parent::__construct();

        $this->jobResps = new ArrayCollection();
    }


    /**
     * @var Camp
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $camp;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="JobResp", mappedBy="job")
     */
    protected $jobResps;


    /**
     * @return Camp
     */
    public function getCamp() {
        return $this->camp;
    }

    public function setCamp($camp) {
        $this->camp = $camp;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @param Period|Day|null $periodOrDay
     * @return ArrayCollection
     */
    public function getJobResps($periodOrDay = null) {
        $filter = null;

        if ($periodOrDay instanceof Period) {
            $filter = function (JobResp $jobResp) use ($periodOrDay) {
                return $jobResp->getPeriod() === $periodOrDay;
            };
        }
        if ($periodOrDay instanceof Day) {
            $filter = function (JobResp $jobResp) use ($periodOrDay) {
                return $jobResp->getDay() === $periodOrDay;
            };
        }

        if ($filter !== null) {
            return $this->jobResps->filter($filter);
        }
        return $this->jobResps;
    }

    public function addJobResp(JobResp $jobResp) {
        $jobResp->setJob($this);
        $this->jobResps->add($jobResp);
    }

    public function removeJobResp(JobResp $jobResp) {
        $jobResp->setJob(null);
        $this->jobResps->removeElement($jobResp);
    }


    /**
     * @param  Day  $day
     * @param  User $user
     * @return bool
     */
    public function isUserResp(Day $day, User $user) {
        $filter = function ($key, JobResp $jobResp) use ($user, $day) {
            return $jobResp->getUser() === $user && $jobResp->getDay() === $day;
        };

        return $this->jobResps->exists($filter);
    }
}
