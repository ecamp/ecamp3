<?php

namespace EcampMaterial\Entity;

use EcampLib\Entity\BaseEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="p_material_items")
 */
class MaterialItem extends BaseEntity
{

    /**
     * @ORM\ManyToOne(targetEntity="EcampCore\Entity\EventPlugin", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="eventPlugin_id", referencedColumnName="id", nullable=true)
     */
    private $eventPlugin;

    /**
     * @ORM\ManyToMany(targetEntity="EcampMaterial\Entity\MaterialList")
     * @ORM\JoinTable(name="p_material_list_item",
     *      joinColumns={@ORM\JoinColumn(name="Item_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="List_id", referencedColumnName="id")}
     *      )
     */
    private $lists;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $quantity;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $text;

    public function __construct(EventPlugin $eventPlugin)
    {
        parent::__construct();
        $this->eventPlugin = $eventPlugin;
        $this->lists =  new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return EcampCore\Entity\EventPlugin
     */
    public function getEventPlugin()
    {
        return $this->eventPlugin;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getLists()
    {
        return $this->lists;
    }

}
