<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * EventTemplate
 * @ORM\Entity()
 * @ORM\Table(name="event_templates", uniqueConstraints={
 * 	 @ORM\UniqueConstraint(name="eventtype_medium_unique", columns={"eventType_id", "medium"})
 * })
 */
class EventTemplate extends BaseEntity {
    public function __construct() {
        parent::__construct();

        $this->eventTemplateContainers = new ArrayCollection();
    }


    /**
     * @var EventType
     * @ORM\ManyToOne(targetEntity="EventType")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $eventType;

    /**
     * @var Medium
     * @ORM\ManyToOne(targetEntity="Medium")
     * @ORM\JoinColumn(name="medium", referencedColumnName="name", nullable=false, onDelete="cascade")
     */
    private $medium;

    /**
     * @var string
     * @ORM\Column(type="string", length=128, nullable=false )
     */
    private $filename;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="EventTemplateContainer", mappedBy="eventTemplate")
     */
    private $eventTemplateContainers;


    /**
     * @return EventType
     */
    public function getEventType() {
        return $this->eventType;
    }

    public function setEventType($eventType) {
        $this->eventType = $eventType;
    }


    /**
     * @return Medium
     */
    public function getMedium(): Medium {
        return $this->medium;
    }

    public function setMedium(Medium $medium): void {
        $this->medium = $medium;
    }


    /**
     * @return string
     */
    public function getFilename(): string {
        return $this->filename;
    }

    public function setFilename(string $filename): void {
        $this->filename = $filename;
    }


    /**
     * @return ArrayCollection
     */
    public function getEventTemplateContainers() {
        return $this->eventTemplateContainers;
    }

    public function addEventTemplateContainer(EventTemplateContainer $eventTemplateContainer) {
        $eventTemplateContainer->setEventTemplate($this);
        $this->eventTemplateContainers->add($eventTemplateContainer);
    }

    public function removeEventTemplateContainer(EventTemplateContainer $eventTemplateContainer) {
        $eventTemplateContainer->setEventTemplate(null);
        $this->eventTemplateContainers->removeElement($eventTemplateContainer);
    }
}
