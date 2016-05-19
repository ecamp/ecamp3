<?php

namespace EcampCourseAim\Entity;

use EcampCore\Entity\Camp;
use EcampCore\Entity\CampType;
use EcampLib\Entity\BaseEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="p_aim_template")
 */
class Template extends BaseEntity
{
    /**
     * @ORM\OneToMany(targetEntity="TemplateItem", mappedBy="list")
     */
    private $items;

    /**
     * @ORM\ManyToOne(targetEntity="EcampCore\Entity\CampType")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $campType;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * revision number of the aim template list (higher=newer)
     */
    private $revision;

    public function __construct(CampType $campType)
    {
        parent::__construct();
        $this->campType = $campType;
        $this->items =  new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return \EcampCore\Entity\CampType
     */
    public function getCampType()
    {
        return $this->campType;
    }

    public function setRevision($revision)
    {
        $this->revision = $revision;
    }

    public function getRevision()
    {
        return $this->revision;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param TemplateItem $item
     */
    public function addItem(TemplateItem $item)
    {
        if ($this->items->contains($item)) {
            return;
        }
        $this->items->add($item);
        $item->addList($this);
    }

    /**
     * @param AimItem $item
     */
    public function removeItem(TemplateItem $item)
    {
        if (!$this->items->contains($item)) {
            return;
        }

        $this->items->removeElement($item);
        $item->removeList($this);
    }
}
