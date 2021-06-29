<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
#[ApiResource]
class ContentNode extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\OneToOne(targetEntity="AbstractContentNodeOwner", mappedBy="rootContentNode", cascade={"persist", "remove"})
     */
    public ?AbstractContentNodeOwner $owner = null;

    /**
     * @ORM\ManyToOne(targetEntity="ContentNode", inversedBy="rootDescendants")
     * TODO make not null, and get fixtures to run
     * @ORM\JoinColumn(nullable=true)
     */
    public ?ContentNode $root;

    /**
     * @ORM\OneToMany(targetEntity="ContentNode", mappedBy="root")
     *
     * @var ContentNode[]
     */
    public $rootDescendants;

    /**
     * @ORM\ManyToOne(targetEntity="ContentNode", inversedBy="children")
     */
    public ?ContentNode $parent = null;

    /**
     * @ORM\OneToMany(targetEntity="ContentNode", mappedBy="parent")
     *
     * @var ContentNode[]
     */
    public $children;

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
     * @ORM\ManyToOne(targetEntity="ContentType")
     * @ORM\JoinColumn(nullable=false)
     */
    public ?ContentType $contentType = null;

    /**
     * @ORM\OneToMany(targetEntity="MaterialItem", mappedBy="contentNode")
     */
    public $materialItems;

    public function __construct() {
        $this->rootDescendants = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->addRootDescendant($this);
    }

    public function getCamp(): ?Camp {
        $root = $this->root;
        $owner = $root->owner;

        if ($owner instanceof BelongsToCampInterface) {
            return $owner->getCamp();
        }

        return null;
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

    public function getMaterialItems(): array {
        return $this->materialItems->getValues();
    }

    public function addMaterialItem(MaterialItem $materialItem): self {
        if (!$this->materialItems->contains($materialItem)) {
            $this->materialItems[] = $materialItem;
            $materialItem->contentNode = $this;
        }

        return $this;
    }

    public function removeMaterialItem(MaterialItem $materialItem): self {
        if ($this->materialItems->removeElement($materialItem)) {
            if ($materialItem->contentNode === $this) {
                $materialItem->contentNode = null;
            }
        }

        return $this;
    }
}
