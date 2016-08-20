<?php

namespace EcampCourseAim\Entity;

use EcampCore\Entity\Camp;
use EcampCore\Entity\EventPlugin;

use EcampLib\Entity\BaseEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="p_aim_item")
 */
class Item extends BaseEntity
{

    /**
     * @var Item
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="children")
     */
    private $parent;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Item", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="EcampCore\Entity\Camp", cascade={"persist","remove"})
     */
    private $camp;


    /**
     * @ORM\ManyToMany(targetEntity="EcampCore\Entity\EventPlugin", cascade={"persist","remove"})
     * @ORM\JoinTable(name="p_aim_item_event",
     *      joinColumns={@ORM\JoinColumn(name="Item_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="EventPlugin_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    public $eventPlugins;


    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $text;

    public function __construct(Camp $camp)
    {
        parent::__construct();
        $this->camp = $camp;
        $this->events = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getChildren(){
        return $this->children;
    }

    public function isLinkedToEventPlugin($eventPlugin)
    {
        return $this->eventPlugins->contains($eventPlugin);
    }


    /**
     * @param EventPlugin $eventPlugin
     */
    public function addEventPlugin(EventPlugin $eventPlugin)
    {
        if ($this->eventPlugins->contains($eventPlugin)) {
            return;
        }
        $this->eventPlugins->add($eventPlugin);
    }

    /**
     * @param EventPlugin $eventPlugin
     */
    public function removeEventPlugin(EventPlugin $eventPlugin)
    {
        if (!$this->eventPlugins->contains($eventPlugin)) {
            return;
        }

        $this->eventPlugins->removeElement($eventPlugin);
    }
}
