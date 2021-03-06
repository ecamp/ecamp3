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

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Validator\AssertBelongsToSameCamp;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A person that has some whole-day responsibility on a day in the camp.
 *
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="day_campCollaboration_unique", columns={"dayId", "campCollaborationId"})
 * })
 */
#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'delete'],
)]
#[ApiFilter(SearchFilter::class, properties: ['day'])]
#[UniqueEntity(fields: ['day', 'campCollaboration'])]
class DayResponsible extends BaseEntity implements BelongsToCampInterface {
    /**
     * The day on which the person is responsible.
     *
     * @ORM\ManyToOne(targetEntity="Day", inversedBy="dayResponsibles")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    #[Assert\NotNull]
    #[ApiProperty(example: '/days/1a2b3c4d')]
    public ?Day $day = null;

    /**
     * The person that is responsible. Must belong to the same camp as the day's period.
     *
     * @ORM\ManyToOne(targetEntity="CampCollaboration", inversedBy="dayResponsibles")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    #[AssertBelongsToSameCamp]
    #[ApiProperty(example: '/camp_collaborations/1a2b3c4d')]
    public ?CampCollaboration $campCollaboration = null;

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this->day?->getCamp();
    }
}
