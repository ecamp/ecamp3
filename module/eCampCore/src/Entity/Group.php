<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="eCamp\Core\Repository\GroupRepository")
 * @ORM\Table(name="groups",
 *   indexes={@ORM\Index(name="group_name_idx", columns={"name"})},
 *   uniqueConstraints={@ORM\UniqueConstraint(
 *     name="group_parent_name_unique",columns={"parent_id","name"}
 *   )}
 * )
 * @ORM\HasLifecycleCallbacks
 */
class Group extends AbstractCampOwner
{
    public function __construct()
    {
        parent::__construct();

        $this->children = new ArrayCollection();
        $this->memberships = new ArrayCollection();
    }


    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=false )
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $description;

    /**
     * @var Organization
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="groups")
     */
    private $organization;

    /**
     * @var Group
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="children")
     */
    private $parent;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Group", mappedBy="parent")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $children;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="GroupMembership", mappedBy="group", cascade={"all"}, orphanRemoval=true )
     */
    protected $memberships;


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }


    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->name;
    }


    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }


    /**
     * @return Organization
     */
    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function setOrganization(Organization $organization): void
    {
        $this->organization = $organization;
    }


    /**
     * @return Group
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(Group $parent = null): void
    {
        if ($parent != null) {
            $this->organization = $parent->getOrganization();
        }
        $this->parent = $parent;
    }


    public function pathAsArray()
    {
        $path = ($this->parent != null) ? $this->parent->pathAsArray() : [];
        $path[] = $this;

        return $path;
    }


    /**
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function addChild(Group $child)
    {
        $child->setParent($this);
        $this->children->add($child);
    }

    public function removeChild(Group $child)
    {
        $child->setParent(null);
        $this->children->removeElement($child);
    }


    /**
     * @return ArrayCollection
     */
    public function getGroupMemberships(): ArrayCollection
    {
        return $this->memberships;
    }

    public function addGroupMembership(GroupMembership $membership)
    {
        $membership->setGroup($this);
        $this->memberships->add($membership);
    }

    public function removeGroupMembership(GroupMembership $membership)
    {
        $membership->setGroup(null);
        $this->memberships->removeElement($membership);
    }
}
