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
    private ?Activity $owner = null;

    /**
     * @ORM\ManyToOne(targetEntity=ContentNode::class, inversedBy="rootDescendants")
     * TODO make not null, and get fixtures to run
     * @ORM\JoinColumn(nullable=true)
     */
    private ContentNode $root;

    /**
     * @ORM\OneToMany(targetEntity=ContentNode::class, mappedBy="root")
     */
    private Collection $rootDescendants;

    /**
     * @ORM\ManyToOne(targetEntity=ContentNode::class, inversedBy="children")
     */
    private ?ContentNode $parent = null;

    /**
     * @ORM\OneToMany(targetEntity=ContentNode::class, mappedBy="parent")
     */
    private Collection $children;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $slot = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $position = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private array $jsonConfig = [];

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $instanceName = null;

    /**
     * @ORM\ManyToOne(targetEntity=ContentType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ?ContentType $contentType = null;

    public function __construct() {
        $this->rootDescendants = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->addRootDescendant($this);
    }

    public function getOwner(): ?Activity {
        return $this->owner;
    }

    public function setOwner(?Activity $owner): self {
        $this->owner = $owner;

        return $this;
    }

    public function getRoot(): ?self {
        return $this->root;
    }

    public function setRoot(self $root): self {
        $this->root = $root;

        return $this;
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
            $rootDescendant->setRoot($this);
        }

        return $this;
    }

    public function removeRootDescendant(self $rootDescendant): self {
        if ($this->rootDescendants->removeElement($rootDescendant)) {
            // set the owning side to null (unless already changed)
            if ($rootDescendant->getRoot() === $this) {
                $rootDescendant->setRoot(null);
            }
        }

        return $this;
    }

    public function getParent(): ?self {
        return $this->parent;
    }

    public function setParent(?self $parent): self {
        $this->parent = $parent;

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
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getSlot(): ?string {
        return $this->slot;
    }

    public function setSlot(?string $slot): self {
        $this->slot = $slot;

        return $this;
    }

    public function getPosition(): ?int {
        return $this->position;
    }

    public function setPosition(?int $position): self {
        $this->position = $position;

        return $this;
    }

    public function getJsonConfig(): ?array {
        return $this->jsonConfig;
    }

    public function setJsonConfig(array $jsonConfig): self {
        $this->jsonConfig = $jsonConfig;

        return $this;
    }

    public function getInstanceName(): ?string {
        return $this->instanceName;
    }

    public function setInstanceName(?string $instanceName): self {
        $this->instanceName = $instanceName;

        return $this;
    }

    public function getContentType(): ?ContentType {
        return $this->contentType;
    }

    public function setContentType(?ContentType $contentType): self {
        $this->contentType = $contentType;

        return $this;
    }
}
