<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
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
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * @ORM\Entity(repositoryClass="EcampCore\Repository\CampRepository")
 * @ORM\Table(name="camps",
 *   uniqueConstraints={@ORM\UniqueConstraint(name="group_name_unique",columns={"group_id", "name"}),
 *                      @ORM\UniqueConstraint(name="owner_name_unique",columns={"owner_id", "name"})}
 *   )
 */
class Camp extends BaseEntity
    implements ResourceInterface
{
    const VISIBILITY_PUBLIC = 'public';
    const VISIBILITY_CONTRIBUTORS = 'contributors';

    public function __construct(CampType $campType)
    {
        parent::__construct();

        $this->visibility = self::VISIBILITY_PUBLIC;

        $this->campType = $campType;
        $this->collaborations 	= new \Doctrine\Common\Collections\ArrayCollection();
        $this->periods 			= new \Doctrine\Common\Collections\ArrayCollection();
        $this->eventCategories	= new \Doctrine\Common\Collections\ArrayCollection();
        $this->events    		= new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Short identifier, unique inside group or user
     * @var string
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private $motto;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=false, options={"default" = "public"})
     */
    private $visibility;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=false)
     */
    private $creator;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="mycamps")
     */
    private $owner;

    /**
     * @var Group
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="camps")
     */
    private $group;

    /**
     * @var CampType
     * @ORM\ManyToOne(targetEntity="CampType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campType;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="CampCollaboration", mappedBy="camp")
     */
    protected $collaborations;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Period", mappedBy="camp")
     * @ORM\OrderBy({"start" = "ASC"})
     */
    protected $periods;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="EventCategory", mappedBy="camp")
     */
    protected $eventCategories;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Event", mappedBy="camp")
     */
    protected $events;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setMotto($motto)
    {
        $this->motto = $motto;
    }

    public function getMotto()
    {
        return $this->motto;
    }

    public function setCreator(User $creator)
    {
        $this->creator = $creator;
    }

    public function getVisibility()
    {
        return $this->visibility;
    }

    public function setVisibility($visibility)
    {
        if (in_array($visibility, array(self::VISIBILITY_PUBLIC, self::VISIBILITY_CONTRIBUTORS))) {
            $this->visibility = $visibility;
        } else {
            throw new \Exception("Unallowed Value for Camp::Visibility ($visibility)");
        }
    }

    /**
     * @return User
     */
    public function getCreator()
    {
        return $this->creator;
    }

    public function setGroup(Group $group)
    {
        $this->_setGroup($group);
        $this->_setOwner(null);
    }

    private function _setGroup(Group $group = null)
    {
        if ($this->owner != null) {
            $this->owner->removeFromList('myCamps', $this);
        }

        $this->group = $group;

        if ($this->group != null) {
            $this->group->addToList('camps', $this);
        }
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    public function setOwner(User $owner)
    {
        $this->_setGroup(null);
        $this->_setOwner($owner);
    }

    private function _setOwner(User $user = null)
    {
        if ($this->group != null) {
            $this->group->removeFromList('camps', $this);
        }

        $this->owner = $user;

        if ($this->owner != null) {
            $this->owner->addToList('myCamps', $this);
        }
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    public function belongsToUser()
    {
        return isset($this->owner);
    }

    /**
     * @return CampType
     */
    public function getCampType()
    {
        return $this->campType;
    }

    /**
     * @return array
     */
    public function getPeriods()
    {
        return $this->periods;
    }

    /**
     * @return array
     */
    public function getEventCategories()
    {
        return $this->eventCategories;
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    public function getRange()
    {
        if ($this->getPeriods()->count() == 0) {
            return "-";
        }

        return $this->getPeriods()->first()->getStart()->format("d.m.Y") . ' - ' . $this->getPeriods()->last()->getEnd()->format("d.m.Y");
    }

    public function getStart()
    {
        if ($this->getPeriods()->count() == 0) {
            return null;
        }

        return $this->getPeriods()->first()->getStart();
    }

    public function getEnd()
    {
        if ($this->getPeriods()->count() == 0) {
            return null;
        }

        return $this->getPeriods()->last()->getEnd();
    }

    /**
     * @return CampCollaborationHelper
     */
    public function campCollaboration()
    {
        return new CampCollaborationHelper($this->collaborations);
    }

    public function getResourceId()
    {
        return 'EcampCore\Entity\Camp';
    }

}
