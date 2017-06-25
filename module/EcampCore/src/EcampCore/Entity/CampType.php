<?php
/*
 * Copyright (C) 2012 Urban Suppiger
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
 * CampType
 * @ORM\Entity
 * @ORM\Table(name="camp_types")
 */
class CampType extends BaseEntity
{
    const ORGANIZATION_JS 	    = "J+S";
    const ORGANIZATION_PBS 	    = "PBS";
    const ORGANIZATION_JUBLA	= "JUBLA";
    const ORGANIZATION_CEVI	    = "CEVI";
    const ORGANIZATION_OTHER  	= "Other";
    const ORGANIZATION_NONE		= "None";

    public function __construct($name, $isCourse, $organization, $isJS)
    {
        parent::__construct();

        $this->name = $name;
        $this->isCourse = $isCourse;
        $this->organization = $organization;
        $this->isJS = $isJS;

        $this->eventTypes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="EventType", mappedBy="campTypes")
     */
    private $eventTypes;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * true for training courses
     * false for camps
     */
    private $isCourse;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     * Main organization (Verband) of the course/camp
     */
    private $organization;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * true for camps/courses registered with J+S
     */
    private $isJS;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @return boolean
     */
    public function isCourse()
    {
        return $this->isCourse;
    }

    /**
     * @return boolean
     */
    public function isJS()
    {
        return $this->isJS;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getEventTypes()
    {
        return $this->eventTypes;
    }
}
