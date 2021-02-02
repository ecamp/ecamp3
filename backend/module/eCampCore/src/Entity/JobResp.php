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
 */
class JobResp extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\ManyToOne(targetEntity="Day")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private ?Day $day;

    /**
     * @ORM\ManyToOne(targetEntity="Job")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private ?Job $job;

    /**
     * @ORM\ManyToOne(targetEntity="CampCollaboration")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?CampCollaboration $campCollaboration;

    public function getDay(): ?Day {
        return $this->day;
    }

    public function setDay(?Day $day) {
        $this->day = $day;
    }

    public function getPeriod(): ?Period {
        return $this->day->getPeriod();
    }

    public function getCamp(): ?Camp {
        return $this->day->getCamp();
    }

    public function getJob(): ?Job {
        return $this->job;
    }

    public function setJob(?Job $job) {
        $this->job = $job;
    }

    public function getCampCollaboration(): ?CampCollaboration {
        return $this->campCollaboration;
    }

    public function setCampCollaboration(?CampCollaboration $collaboration) {
        $this->campCollaboration = $collaboration;
    }

    public function getUser(): ?User {
        return (null != $this->campCollaboration) ? $this->campCollaboration->getUser() : null;
    }
}
