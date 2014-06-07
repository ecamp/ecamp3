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
use Zend\Form\Annotation as Form;

use EcampLib\Entity\BaseEntity;
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * @ORM\Entity(repositoryClass="EcampCore\Repository\CampRepository")
 * @ORM\Table(name="camps",
 *   uniqueConstraints={ @ORM\UniqueConstraint(name="owner_name_unique", columns={"owner_id", "name"}) }
 *   )
 *
 * @Form\Name("camp")
 */
class Camp extends BaseEntity
    implements ResourceInterface
{
    const VISIBILITY_PUBLIC = 'public';
    const VISIBILITY_CONTRIBUTORS = 'contributors';

    public function __construct()
    {
        parent::__construct();

        $this->visibility = self::VISIBILITY_PUBLIC;

        $this->collaborations 	= new \Doctrine\Common\Collections\ArrayCollection();
        $this->periods 			= new \Doctrine\Common\Collections\ArrayCollection();
        $this->eventCategories	= new \Doctrine\Common\Collections\ArrayCollection();
        $this->events    		= new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Short identifier, unique inside group or user
     * @var string
     * @ORM\Column(type="string", length=32, nullable=false)
     * @Form\Validator({ "name": "StringLength", "options": { "min":"8" } })
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     * @Form\Validator({ "name": "StringLength", "options": { "min":"8" } })
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(type="string", length=128, nullable=false)
     * @Form\Validator({ "name": "StringLength", "options": { "min":"8" } })
     */
    protected $motto;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=false, options={"default" = "public"})
     * @Form\Exclude
     */
    private $visibility;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=false)
     * @Form\Exclude
     */
    private $creator;

    /**
     * @var AbstractCampOwner
     * @ORM\ManyToOne(targetEntity="AbstractCampOwner", inversedBy="ownedCamps")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false)
     *
     * @Form\Options({
     *      "property": "displayName",
     *      "is_method": "true",
     *      "find_method": { "name": "findPossibleCampOwner", "params": {} },
     *      "empty_option": "Select ..."
     * })
     */
    private $owner;

    /**
     * @var CampType
     * @ORM\ManyToOne(targetEntity="CampType")
     * @ORM\JoinColumn(nullable=false)
     * @Form\Options({
     *      "property": "name",
     *      "empty_option": "Select ..."
     * })
     */
    private $campType;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="CampCollaboration", mappedBy="camp")
     * @Form\Exclude
     */
    protected $collaborations;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Period", mappedBy="camp")
     * @ORM\OrderBy({"start" = "ASC"})
     * @Form\Exclude
     */
    protected $periods;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="EventCategory", mappedBy="camp")
     * @Form\Exclude
     */
    protected $eventCategories;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Event", mappedBy="camp")
     * @Form\Exclude
     */
    protected $events;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Job", mappedBy="camp")
     * @Form\Exclude
     */
    protected $jobs;

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

    public function setCreator(User $creator)
    {
        $this->creator = $creator;
    }

    /**
     * @return AbstractCampOwner
     */
    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner(AbstractCampOwner $owner)
    {
        $this->owner = $owner;
    }

    public function belongsToUser()
    {
        return $this->owner instanceof User;
    }

    public function belongsToGroup()
    {
        return $this->owner instanceof Group;
    }

    /**
     * @return CampType
     */
    public function getCampType()
    {
        return $this->campType;
    }

    public function setCampType(CampType $campType)
    {
        $this->campType = $campType;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPeriods()
    {
        return $this->periods;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getEventCategories()
    {
        return $this->eventCategories;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    public function getRange()
    {
        if ($this->getPeriods()->count() == 0) {
            return "-";
        }

        $start = $this->getPeriods()->first()->getStart();
        $end = $this->getPeriods()->last()->getEnd();

        if ($start->format("Y") == $end->format("Y")) {
            if ($start->format("m") == $end->format("m")) {
                return $start->format("d.") . ' - ' . $end->format('d.m.Y');
            } else {
                return $start->format("d.m.") . ' - ' . $end->format('d.m.Y');
            }
        } else {
            return $start->format("d.m.Y") . ' - ' . $end->format('d.m.Y');
        }
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
