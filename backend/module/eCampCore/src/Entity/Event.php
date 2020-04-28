<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Core\Plugin\PluginStrategyProvider;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="events")
 */
class Event extends BaseEntity {
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EventPlugin", mappedBy="event", cascade={"all"}, orphanRemoval=true)
     */
    protected $eventPlugins;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EventInstance", mappedBy="event", orphanRemoval=true)
     */
    protected $eventInstances;

    /**
     * @var Camp
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
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

    public function __construct() {
        parent::__construct();

        $this->eventPlugins = new ArrayCollection();
        $this->eventInstances = new ArrayCollection();
    }

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
        return (null !== $this->eventCategory) ? $this->eventCategory->getEventType() : null;
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

        if (null !== $this->getEventType() && $this->getEventPlugins()->isEmpty()) {
            $this->createDefaultEventPlugins();
        }
    }

    /**
     * Replicates the default plugin structure given by the EventType.
     */
    public function createDefaultEventPlugins(PluginStrategyProvider $pluginStrategyProvider = null) {
        foreach ($this->getEventType()->getEventTypePlugins() as $eventTypePlugin) {
            // TO DO: getMinNumberPluginInstances probably not ideal, beneath min & max there should also be a default value
            for ($idx = 0; $idx < $eventTypePlugin->getMinNumberPluginInstances(); ++$idx) {
                /** @var Plugin $plugin */
                $plugin = $eventTypePlugin->getPlugin();
                $pluginName = $plugin->getName().' ';
                $pluginName .= str_pad($idx + 1, 2, '0');

                $eventPlugin = new EventPlugin();
                $eventPlugin->setEventTypePlugin($eventTypePlugin);
                $eventPlugin->setInstanceName($pluginName);

                if ($pluginStrategyProvider) {
                    $eventPlugin->setPluginStrategyProvider($pluginStrategyProvider);
                }

                $this->addEventPlugin($eventPlugin);
            }
        }
    }
}
