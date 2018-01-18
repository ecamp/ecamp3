<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;


/**
 * @ORM\Entity()
 * @ORM\Table(name="event_plugins")
 */
class EventPlugin extends BaseEntity
{
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
     * @var Plugin
     * @ORM\ManyToOne(targetEntity="Plugin")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $plugin;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $instanceName;


    /**
     * @return Event
     */
    public function getEvent(): Event {
        return $this->event;
    }

    public function setEvent(Event $event): void {
        $this->event = $event;
    }


    /**
     * @return Plugin
     */
    public function getPlugin(): Plugin {
        return $this->plugin;
    }

    public function setPlugin(Plugin $plugin): void {
        $this->plugin = $plugin;
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
