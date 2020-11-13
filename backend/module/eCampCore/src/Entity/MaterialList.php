<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 */
class MaterialList extends BaseEntity {

    /**
     * @var Camp
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $camp;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="MaterialItem", mappedBy="materialList")
     */
    protected $materialItems;

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
        $materialItem->setPeriod($this);
        $this->materialItems->add($materialItem);
    }

    public function removeMaterialItem(MaterialItem $materialItem) {
        $materialItem->setPeriod(null);
        $this->materialItems->removeElement($materialItem);
    }

}
