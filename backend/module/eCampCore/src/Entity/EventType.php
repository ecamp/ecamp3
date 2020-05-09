<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * EventType.
 *
 * @ORM\Entity
 * @ORM\Table(name="event_types")
 */
class EventType extends BaseEntity {
    const TEMPLATE_GENERAL = 'General';

    /**
     * @ORM\ManyToMany(targetEntity="CampType", mappedBy="eventTypes")
     */
    protected $campTypes;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EventTypePlugin", mappedBy="eventType", orphanRemoval=true)
     */
    protected $eventTypePlugins;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EventTypeFactory", mappedBy="eventType")
     */
    protected $eventTypeFactories;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $template = self::TEMPLATE_GENERAL;

    /**
     * @var string
     * @ORM\Column(type="string", length=8, nullable=false)
     */
    private $defaultColor = '#1fa2df';

    /**
     * @var string
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $defaultNumberingStyle;

    public function __construct() {
        parent::__construct();

        $this->campTypes = new ArrayCollection();
        $this->eventTypePlugins = new ArrayCollection();
        $this->eventTypeFactories = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getTemplate() {
        return $this->name;
    }

    public function setTemplate($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDefaultColor() {
        return $this->defaultColor;
    }

    public function setDefaultColor($defaultColor) {
        $this->defaultColor = $defaultColor;
    }

    /**
     * @return string
     */
    public function getDefaultNumberingStyle() {
        return $this->defaultNumberingStyle;
    }

    public function setDefaultNumberingStyle($defaultNumberingStyle) {
        $this->defaultNumberingStyle = $defaultNumberingStyle;
    }

    /**
     * @return ArrayCollection
     */
    public function getEventTypePlugins() {
        return $this->eventTypePlugins;
    }

    public function addEventTypePlugin(EventTypePlugin $eventTypePlugin) {
        $eventTypePlugin->setEventType($this);
        $this->eventTypePlugins->add($eventTypePlugin);
    }

    public function removeEventTypePlugin(EventTypePlugin $eventTypePlugin) {
        $eventTypePlugin->setEventType(null);
        $this->eventTypePlugins->removeElement($eventTypePlugin);
    }

    /**
     * @return ArrayCollection
     */
    public function getEventTypeFactories() {
        return $this->eventTypeFactories;
    }

    public function addEventTypeFactory(EventTypeFactory $factory) {
        $factory->setEventType($this);
        $this->eventTypeFactories->add($factory);
    }

    public function removeEventTypeFactory(EventTypeFactory $factory) {
        $factory->setEventType(null);
        $this->eventTypeFactories->removeElement($factory);
    }
}
