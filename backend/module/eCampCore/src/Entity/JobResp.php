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

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="job_resps")
 */
class JobResp extends BaseEntity {
    /**
     * @var Day
     * @ORM\ManyToOne(targetEntity="Day")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $day;

    /**
     * @var Job
     * @ORM\ManyToOne(targetEntity="Job")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $job;

    /**
     * @var CampCollaboration
     * @ORM\ManyToOne(targetEntity="CampCollaboration")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campCollaboration;

    public function __construct() {
        parent::__construct();
    }

    /** @return Day */
    public function getDay() {
        return $this->day;
    }

    public function setDay(Day $day) {
        $this->day = $day;
    }

    /** @return Period */
    public function getPeriod() {
        return $this->day->getPeriod();
    }

    /** @return Camp */
    public function getCamp() {
        return $this->day->getCamp();
    }

    /** @return Job */
    public function getJob() {
        return $this->job;
    }

    public function setJob(Job $job) {
        $this->job = $job;
    }

    /** @return CampCollaboration */
    public function getCampCollaboration() {
        return $this->campCollaboration;
    }

    public function setCampCollaboration(CampCollaboration $collaboration) {
        $this->campCollaboration = $collaboration;
    }

    /** @return User */
    public function getUser() {
        return $this->campCollaboration->getUser();
    }
}
