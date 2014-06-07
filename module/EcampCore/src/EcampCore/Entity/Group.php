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
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * @ORM\Entity(repositoryClass="EcampCore\Repository\GroupRepository")
 * @ORM\Table(name="groups", indexes={@ORM\Index(name="group_name_idx", columns={"name"})}, uniqueConstraints={@ORM\UniqueConstraint(name="group_parent_name_unique",columns={"parent_id","name"})})
 * @ORM\HasLifecycleCallbacks
 */
class Group
    extends AbstractCampOwner
    implements ResourceInterface
{
    public function __construct(Group $parent = null)
    {
        parent::__construct();

        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->memberships = new \Doctrine\Common\Collections\ArrayCollection();

        $this->setParent($parent);
    }

    /**
     * Short identifier, unique inside parent group
     * @var string
     * @ORM\Column(type="string", length=32, nullable=false )
     */
    private $name;

    /**
     * @var Group
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="children")
     */
    private $parent;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Group", mappedBy="parent")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $children;

    /**
     * @ORM\Column(type="string", length=64, nullable=false )
     */
    private $description;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="GroupMembership", mappedBy="group", cascade={"all"}, orphanRemoval=true )
     */
    protected $memberships;

    /**
     * @var Image
     * @ORM\OneToOne(targetEntity="Image")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName( $name )
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription( $description )
    {
        $this->description = $description;
    }

    public function getDisplayName()
    {
        return $this->name;
    }

    /**
     * @return Group
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(Group $parent = null)
    {
        if ($this->parent != null) {
            $this->parent->children->removeElement($this);
        }

        $this->parent = $parent;

        if ($this->parent != null) {
            $this->parent->children->add($this);
        }
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function hasChildren()
    {
        return ! $this->children->isEmpty();
    }

    public function getPath($seperator = ' > ')
    {
        $groups = $this->getPathAsArray(true);
        $groupNames = array_map(function($g){ return $g->getName(); }, $groups);

        return implode($seperator, $groupNames);
    }

    /**
     * @return array
     */
    public function getPathAsArray( $include_self = false )
    {
        $path = array();
        $group = $this;

        if($include_self)
            $path[] = $group;

        while ( $group->getParent() != null ) {
            $group = $group->getParent();
            $path[] = $group;
        }

        return array_reverse($path);
    }

    /**
     * @return \EcampCore\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param  Image                   $image
     * @return \EcampCore\Entity\Group
     */
    public function setImage(Image $image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return \EcampCore\Entity\Group
     */
    public function delImage()
    {
        $this->image = null;

        return $this;
    }

    public function getResourceId()
    {
        return 'EcampCore\Entity\Group';
    }

    /**
     * @return GroupMembershipHelper
     */
    public function groupMembership()
    {
        return new GroupMembershipHelper($this->memberships);
    }

    /**
     * @ORM\PreRemove
     */
    public function preRemove()
    {
        $this->setParent(null);
    }
}
