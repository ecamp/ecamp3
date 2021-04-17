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
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 */
class Job extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\OneToMany(targetEntity="JobResp", mappedBy="job")
     */
    protected Collection $jobResps;

    /**
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private ?Camp $camp = null;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $name = null;

    public function __construct() {
        parent::__construct();

        $this->jobResps = new ArrayCollection();
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    public function setCamp(?Camp $camp): void {
        $this->camp = $camp;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): void {
        $this->name = $name;
    }

    /**
     * @param null|Day|Period $periodOrDay
     */
    public function getJobResps($periodOrDay = null): Collection {
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

        if (null !== $filter) {
            return $this->jobResps->filter($filter);
        }

        return $this->jobResps;
    }

    public function addJobResp(JobResp $jobResp): void {
        $jobResp->setJob($this);
        $this->jobResps->add($jobResp);
    }

    public function removeJobResp(JobResp $jobResp): void {
        $jobResp->setJob(null);
        $this->jobResps->removeElement($jobResp);
    }

    public function isUserResp(Day $day, User $user): bool {
        $filter = function ($key, JobResp $jobResp) use ($user, $day) {
            return $jobResp->getUser() === $user && $jobResp->getDay() === $day;
        };

        return $this->jobResps->exists($filter);
    }
}
