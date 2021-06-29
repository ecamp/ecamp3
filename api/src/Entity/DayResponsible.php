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

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
#[ApiResource]
class DayResponsible extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\ManyToOne(targetEntity="Day", inversedBy="dayResponsibles")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    public ?Day $day;

    /**
     * @ORM\ManyToOne(targetEntity="CampCollaboration", inversedBy="dayResponsibles")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    public ?CampCollaboration $campCollaboration;

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this->day->getCamp();
    }
}
