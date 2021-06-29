<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
#[ApiResource]
class MaterialList extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\OneToMany(targetEntity="MaterialItem", mappedBy="materialList")
     *
     * @var MaterialItem
     */
    public Collection $materialItems;

    /**
     * @ORM\ManyToOne(targetEntity="Camp", inversedBy="materialLists")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    public ?Camp $camp = null;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    public ?string $materialListPrototypeId = null;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public ?string $name = null;

    public function __construct() {
        $this->materialItems = new ArrayCollection();
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    public function getMaterialItems(): array {
        return $this->materialItems->getValues();
    }

    public function addMaterialItem(MaterialItem $materialItem): self {
        if (!$this->materialItems->contains($materialItem)) {
            $this->materialItems[] = $materialItem;
            $materialItem->materialList = $this;
        }

        return $this;
    }

    public function removeMaterialItem(MaterialItem $materialItem): self {
        if ($this->materialItems->removeElement($materialItem)) {
            if ($materialItem->materialList === $this) {
                $materialItem->materialList = null;
            }
        }

        return $this;
    }
}
