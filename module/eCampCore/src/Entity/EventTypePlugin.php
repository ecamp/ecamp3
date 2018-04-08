<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;
use Zend\Json\Json;

/**
 * EventTypePlugin
 * @ORM\Entity
 * @ORM\Table(name="event_type_plugins")
 */
class EventTypePlugin extends BaseEntity
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @var EventType
     * @ORM\ManyToOne(targetEntity="EventType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eventType;

    /**
     * @var Plugin
     * @ORM\ManyToOne(targetEntity="Plugin")
     * @ORM\JoinColumn(nullable=false)
     */
    private $plugin;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $minNumberPluginInstances;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $maxNumberPluginInstances;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $jsonConfig;


    /**
     * @return EventType
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    public function setEventType($eventType)
    {
        $this->eventType = $eventType;
    }


    /**
     * @return Plugin
     */
    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }

    public function setPlugin(Plugin $plugin): void
    {
        $this->plugin = $plugin;
    }


    /**
     * @return int
     */
    public function getMinNumberPluginInstances(): int
    {
        return $this->minNumberPluginInstances;
    }

    public function setMinNumberPluginInstances(int $minNumberPluginInstances): void
    {
        $this->minNumberPluginInstances = $minNumberPluginInstances;
    }


    /**
     * @return int
     */
    public function getMaxNumberPluginInstances(): int
    {
        return $this->maxNumberPluginInstances;
    }

    public function setMaxNumberPluginInstances(int $maxNumberPluginInstances): void
    {
        $this->maxNumberPluginInstances = $maxNumberPluginInstances;
    }


    /**
     * @return string
     */
    public function getJsonConfig(): string
    {
        return $this->jsonConfig;
    }

    public function setJsonConfig(string $jsonConfig): void
    {
        $this->jsonConfig = $jsonConfig;
    }


    /**
     * @param string $key
     * @return mixed
     */
    public function getConfig($key = null)
    {
        $config = null;
        if ($this->jsonConfig != null) {
            $config = Json::decode($this->jsonConfig);
            if ($key != null) {
                $config = $config->{$key};
            }
        }
        return $config;
    }
}
