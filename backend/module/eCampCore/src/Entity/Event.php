<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="events")
 */
class Event extends BaseEntity {
    public function __construct() {
        parent::__construct();

        $this->eventPlugins = new ArrayCollection();
        $this->eventInstances = new ArrayCollection();
    }

    /**
     * @var Camp
     * @ORM\ManyToOne(targetEntity="Camp")
     */
    private $camp;

    /**
     * @var EventCategory
     * @ORM\ManyToOne(targetEntity="EventCategory")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eventCategory;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $title;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EventPlugin", mappedBy="event", cascade={"all"}, orphanRemoval=true)
     */
    protected $eventPlugins;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EventInstance", mappedBy="event", cascade={"all"}, orphanRemoval=true)
     */
    protected $eventInstances;


    /**
     * @return Camp
     */
    public function getCamp() {
        return $this->camp;
    }

    public function setCamp($camp) {
        $this->camp = $camp;
    }


    /**
     * @return EventCategory
     */
    public function getEventCategory(): EventCategory {
        return $this->eventCategory;
    }

    public function setEventCategory(EventCategory $eventCategory): void {
        $this->eventCategory = $eventCategory;
    }


    /**
     * @return EventType
     */
    public function getEventType() {
        return ($this->eventCategory !== null) ? $this->eventCategory->getEventType() : null;
    }


    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }


    /**
     * @return ArrayCollection
     */
    public function getEventPlugins() {
        return $this->eventPlugins;
    }

    public function addEventPlugin(EventPlugin $eventPlugin) {
        $eventPlugin->setEvent($this);
        $this->eventPlugins->add($eventPlugin);
    }

    public function removeEventPlugin(EventPlugin $eventPlugin) {
        $eventPlugin->setEvent(null);
        $this->eventPlugins->removeElement($eventPlugin);
    }


    /**
     * @return ArrayCollection
     */
    public function getEventInstances() {
        return $this->eventInstances;
    }

    public function addEventInstance(EventInstance $eventInstance) {
        $eventInstance->setEvent($this);
        $this->eventInstances->add($eventInstance);
    }

    public function removeEventInstance(EventInstance $eventInstance) {
        $eventInstance->setEvent(null);
        $this->eventInstances->removeElement($eventInstance);
    }



    /** @ORM\PrePersist */
    public function PrePersist() {
        parent::PrePersist();

        $eventType = $this->getEventType();
        if ($eventType !== null && $this->getEventPlugins()->isEmpty()) {
            $eventType->createDefaultEventPlugins($this);
        }
    }
}
