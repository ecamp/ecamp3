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
use EcampCore\Acl\BelongsToParentResource;

/**
 * Container for an event.
 * - An event has no date/time, as it only describes the program but not when it happens.
 * - An event can either belong to a camp or to a user
 * @ORM\Entity(repositoryClass="EcampCore\Repository\EventRespRepository")
 * @ORM\Table(name="event_resps")
 */
class EventResp
    extends BaseEntity
    implements BelongsToParentResource
{
    public function __construct()
    {
    }

    /**
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(nullable=false)
     */
    private $event;

    /**
     * @ORM\ManyToOne(targetEntity="UserCamp")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userCamp;

    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    public function getParentResource()
    {
        return $this->event;
    }

    public function setUserCamp(UserCamp $userCamp)
    {
        $this->userCamp = $userCamp;
    }

    /**
     * @return UserCamp
     */
    public function getUserCamp()
    {
        return $this->userCamp;
    }

    public function getCamp()
    {
        return $this->event->getCamp();
    }
}
