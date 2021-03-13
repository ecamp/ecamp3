<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 */
class MaterialList extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\OneToMany(targetEntity="MaterialItem", mappedBy="materialList")
     */
    protected Collection $materialItems;

    /**
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private ?Camp $camp = null;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private ?string $materialListPrototypeId = null;

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private ?string $name = null;

    public function __construct() {
        parent::__construct();

        $this->materialItems = new ArrayCollection();
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    /**
     * @internal Do not set the {@link Camp} directly on the Activity. Instead use {@see Camp::addMaterialList()}
     */
    public function setCamp(?Camp $camp): void {
        $this->camp = $camp;
    }

    public function getMaterialListPrototypeId(): ?string {
        return $this->materialListPrototypeId;
    }

    public function setMaterialListPrototypeId(?string $materialListPrototypeId): void {
        $this->materialListPrototypeId = $materialListPrototypeId;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): void {
        $this->name = $name;
    }

    public function getMaterialItems(): Collection {
        return $this->materialItems;
    }

    public function addMaterialItem(MaterialItem $materialItem): void {
        $materialItem->setMaterialList($this);
        $this->materialItems->add($materialItem);
    }

    public function removeMaterialItem(MaterialItem $materialItem): void {
        $materialItem->setMaterialList(null);
        $this->materialItems->removeElement($materialItem);
    }
}
