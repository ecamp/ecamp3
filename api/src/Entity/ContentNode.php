<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ContentNodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContentNodeRepository::class)
 */
#[ApiResource]
class ContentNode extends BaseEntity {
    /**
     * @ORM\OneToOne(targetEntity=Activity::class, inversedBy="rootContentNode", cascade={"persist", "remove"})
     */
    public ?Activity $owner = null;

    /**
     * @ORM\ManyToOne(targetEntity=ContentNode::class, inversedBy="rootDescendants")
     * TODO make not null, and get fixtures to run
     * @ORM\JoinColumn(nullable=true)
     */
    public ?ContentNode $root;

    /**
     * @ORM\OneToMany(targetEntity=ContentNode::class, mappedBy="root")
     */
    public Collection $rootDescendants;

    /**
     * @ORM\ManyToOne(targetEntity=ContentNode::class, inversedBy="children")
     */
    public ?ContentNode $parent = null;

    /**
     * @ORM\OneToMany(targetEntity=ContentNode::class, mappedBy="parent")
     */
    public Collection $children;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public ?string $slot = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public ?int $position = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    public ?array $jsonConfig = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public ?string $instanceName = null;

    /**
     * @ORM\ManyToOne(targetEntity=ContentType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    public ?ContentType $contentType = null;

    public function __construct() {
        $this->rootDescendants = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->addRootDescendant($this);
    }

    /**
     * @return self[]
     */
    public function getRootDescendants(): array {
        return $this->rootDescendants->getValues();
    }

    public function addRootDescendant(self $rootDescendant): self {
        if (!$this->rootDescendants->contains($rootDescendant)) {
            $this->rootDescendants[] = $rootDescendant;
            $rootDescendant->root = $this;
        }

        return $this;
    }

    public function removeRootDescendant(self $rootDescendant): self {
        if ($this->rootDescendants->removeElement($rootDescendant)) {
            // set the owning side to null (unless already changed)
            if ($rootDescendant->root === $this) {
                $rootDescendant->root = null;
            }
        }

        return $this;
    }

    /**
     * @return self[]
     */
    public function getChildren(): array {
        return $this->children->getValues();
    }

    public function addChild(self $child): self {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->parent = $this;
        }

        return $this;
    }

    public function removeChild(self $child): self {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->parent === $this) {
                $child->parent = null;
            }
        }

        return $this;
    }
}
