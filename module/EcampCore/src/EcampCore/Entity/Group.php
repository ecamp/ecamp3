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
use Zend\Permissions\Acl\Resource\ResourceInterface;

/**
 * @ORM\Entity(repositoryClass="EcampCore\Repository\GroupRepository")
 * @ORM\Table(name="groups", indexes={@ORM\Index(name="group_name_idx", columns={"name"})}, uniqueConstraints={@ORM\UniqueConstraint(name="group_parent_name_unique",columns={"parent_id","name"})})
 */
class Group
    extends BaseEntity
    implements CampOwnerInterface
    ,	ResourceInterface
{
    public function __construct()
    {
        parent::__construct();

        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->memberships = new \Doctrine\Common\Collections\ArrayCollection();
        $this->camps = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Short identifier, unique inside parent group
     * @var string
     * @ORM\Column(type="string", length=32, nullable=false )
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="children")
     */
    private $parent;

    /**
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
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Camp", mappedBy="group")
     */
    protected $camps;

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
        $this->name = $name; return $this;
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
        $this->description = $description; return $this;
    }

    /**
     * @return Group
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function setParent( $parent )
    {
        $this->parent = $parent; return $this;
    }

    /**
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function hasChildren()
    {
        return ! $this->children->isEmpty();
    }

    /**
     * @return Camp
     */
    public function getCamps()
    {
        return $this->camps;
    }

    /**
     * @return array
     */
    public function getPathAsArray()
    {
        $path = array();
        $group = $this;

        while ( $group->getParent() != null ) {
            $group = $group->getParent();
            $path[] = $group;
        }

        return array_reverse($path);
    }

    /**
     * @return CoreApi\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return CoreApi\Entity\Group
     */
    public function setImage(Image $image)
    {
        $this->image = $image;	return $this;
    }

    /**
     * @return CoreApi\Entity\Group
     */
    public function delImage()
    {
        $this->image = null;	return $this;
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

}
