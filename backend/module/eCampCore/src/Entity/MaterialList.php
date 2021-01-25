<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 */
class MaterialList extends BaseEntity implements BelongsToCampInterface {
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="MaterialItem", mappedBy="materialList")
     */
    protected $materialItems;

    /**
     * @var Camp
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $camp;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $materialListTemplateId;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    public function __construct() {
        parent::__construct();

        $this->materialItems = new ArrayCollection();
    }

    /**
     * @return Camp
     */
    public function getCamp() {
        return $this->camp;
    }

    /**
     * @internal Do not set the {@link Camp} directly on the Activity. Instead use {@see Camp::addMaterialList()}
     *
     * @param $camp
     */
    public function setCamp($camp) {
        $this->camp = $camp;
    }

    /**
     * @return string
     */
    public function getMaterialListTemplateId() {
        return $this->materialListTemplateId;
    }

    public function setMaterialListTemplateId(string $materialListTemplateId) {
        $this->materialListTemplateId = $materialListTemplateId;
    }

    public function getName() {
        return $this->name;
    }

    public function setName(string $name) {
        $this->name = $name;
    }

    /**
     * @return ArrayCollection
     */
    public function getMaterialItems() {
        return $this->materialItems;
    }

    public function addMaterialItem(MaterialItem $materialItem) {
        $materialItem->setMaterialList($this);
        $this->materialItems->add($materialItem);
    }

    public function removeMaterialItem(MaterialItem $materialItem) {
        $materialItem->setMaterialList(null);
        $this->materialItems->removeElement($materialItem);
    }
}
