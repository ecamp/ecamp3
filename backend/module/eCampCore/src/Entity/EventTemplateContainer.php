<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * EventTemplate
 * @ORM\Entity()
 * @ORM\Table(name="event_template_containers", uniqueConstraints={
 * 	@ORM\UniqueConstraint(
 * 		name="eventTemplate_containerName_unique",
 * 		columns={"eventTemplate_id", "containerName"}
 * 	)
 * })
 */
class EventTemplateContainer extends BaseEntity {
    public function __construct() {
        parent::__construct();
    }

    /**
     * @var EventTemplate
     * @ORM\ManyToOne(targetEntity="EventTemplate")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $eventTemplate;

    /**
     * @var EventTypePlugin
     * @ORM\ManyToOne(targetEntity="EventTypePlugin")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $eventTypePlugin;

    /**
     * @var string
     * @ORM\Column(type="string", length=128, nullable=false )
     */
    private $containerName;

    /**
     * @var string
     * @ORM\Column(type="string", length=128, nullable=false )
     */
    private $filename;


    /**
     * @return EventTemplate
     */
    public function getEventTemplate() {
        return $this->eventTemplate;
    }

    public function setEventTemplate($eventTemplate) {
        $this->eventTemplate = $eventTemplate;
    }


    /**
     * @return EventTypePlugin
     */
    public function getEventTypePlugin(): EventTypePlugin {
        return $this->eventTypePlugin;
    }

    public function setEventTypePlugin(EventTypePlugin $eventTypePlugin): void {
        $this->eventTypePlugin = $eventTypePlugin;
    }


    /**
     * @return string
     */
    public function getContainerName(): string {
        return $this->containerName;
    }

    public function setContainerName(string $containerName): void {
        $this->containerName = $containerName;
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
}
