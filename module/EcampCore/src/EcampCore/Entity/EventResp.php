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
 * Container for an event.
 * - An event has no date/time, as it only describes the program but not when it happens.
 * - An event can either belong to a camp or to a user
 * @ORM\Entity(repositoryClass="EcampCore\Repository\EventRespRepository")
 * @ORM\Table(name="event_resps")
 * @ORM\HasLifecycleCallbacks
 */
class EventResp
    extends BaseEntity
{
    public function __construct(Event $event, CampCollaboration $campCollaboration)
    {
        parent::__construct();

        if ($event->getCamp() != $campCollaboration->getCamp()) {
            throw new \OutOfRangeException(
                "Event [" . $event->getId() . "] " .
                "and CampCollaboration [" . $campCollaboration->getId() . "] " .
                "do not belong to same Camp."
            );
        }

        $this->event = $event;
        $this->campCollaboration = $campCollaboration;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(nullable=false)
     */
    private $event;

    /**
     * @ORM\ManyToOne(targetEntity="CampCollaboration")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campCollaboration;

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return UserCamp
     */
    public function getCampCollaboration()
    {
        return $this->campCollaboration;
    }

    /**
     * @return Camp
     */
    public function getCamp()
    {
        return $this->event->getCamp();
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->campCollaboration->getUser();
    }

    /**
     * @ORM\PrePersist
     */
    public function PrePersist()
    {
        parent::PrePersist();

        $this->event->addToList('eventResps', $this);
        $this->campCollaboration->addToList('eventResps', $this);
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemove()
    {
        $this->event->removeFromList('eventResps', $this);
        $this->campCollaboration->removeFromList('eventResps', $this);
    }

}
