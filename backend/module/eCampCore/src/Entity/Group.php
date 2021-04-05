<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="eCamp\Core\Repository\GroupRepository")
 * @ORM\Table(
 *     indexes={@ORM\Index(name="group_name_idx", columns={"name"}, options={"lengths": {128}})},
 *     uniqueConstraints={@ORM\UniqueConstraint(
 *         name="group_parent_name_unique", columns={"parentId", "name"}
 *     )}
 * )
 * @ORM\HasLifecycleCallbacks
 */
class Group extends AbstractCampOwner {
    /**
     * @ORM\OneToMany(targetEntity="GroupMembership", mappedBy="group", orphanRemoval=true)
     */
    protected Collection $memberships;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private ?string $description = null;

    /**
     * @ORM\ManyToOne(targetEntity="Organization")
     */
    private ?Organization $organization = null;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="children")
     * @ORM\JoinColumn(nullable=true, onDelete="cascade")
     */
    private ?Group $parent = null;

    /**
     * @ORM\OneToMany(targetEntity="Group", mappedBy="parent")
     * @ORM\OrderBy({"name": "ASC"})
     */
    private Collection $children;

    public function __construct() {
        parent::__construct();

        $this->memberships = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): void {
        $this->name = $name;
    }

    public function getDisplayName(): ?string {
        return $this->name;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function getOrganization(): ?Organization {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): void {
        $this->organization = $organization;
    }

    public function getParent(): ?Group {
        return $this->parent;
    }

    public function setParent(?Group $parent): void {
        if (null != $parent) {
            $this->organization = $parent->getOrganization();
        }
        $this->parent = $parent;
    }

    public function pathAsArray(): array {
        $path = (null != $this->parent) ? $this->parent->pathAsArray() : [];
        $path[] = $this;

        return $path;
    }

    public function getChildren(): Collection {
        return $this->children;
    }

    public function addChild(Group $child): void {
        $child->setParent($this);
        $this->children->add($child);
    }

    public function removeChild(Group $child): void {
        $child->setParent(null);
        $this->children->removeElement($child);
    }

    public function getGroupMemberships(): Collection {
        return $this->memberships;
    }

    public function addGroupMembership(GroupMembership $membership): void {
        $membership->setGroup($this);
        $this->memberships->add($membership);
    }

    public function removeGroupMembership(GroupMembership $membership): void {
        $membership->setGroup(null);
        $this->memberships->removeElement($membership);
    }
}
