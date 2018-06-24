<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Core\Hydrator\EventTypePluginHydrator;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="event_plugins")
 */
class EventPlugin extends BaseEntity {
    public function __construct() {
        parent::__construct();
    }

    /**
     * @var Event
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    public $event;

    /**
     * @var EventTypePlugin
     * @ORM\ManyToOne(targetEntity="EventTypePlugin")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $eventTypePlugin;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $instanceName;


    /**
     * @return Event
     */
    public function getEvent() {
        return $this->event;
    }

    public function setEvent($event) {
        $this->event = $event;
    }


    /**
     * @return EventTypePlugin
     */
    public function getEventTypePlugin() {
        return $this->eventTypePlugin;
    }

    public function setEventTypePlugin(EventTypePlugin $eventTypePlugin): void {
        $this->eventTypePlugin = $eventTypePlugin;
    }


    /**
     * @return Plugin
     */
    public function getPlugin() {
        return ($this->eventTypePlugin != null) ? $this->eventTypePlugin->getPlugin() : null;
    }


    /**
     * @return string
     */
    public function getInstanceName() {
        return $this->instanceName;
    }

    public function setInstanceName($instanceName): void {
        $this->instanceName = $instanceName;
    }
}
