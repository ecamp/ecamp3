<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * EventTypeFactory
 * @ORM\Entity()
 * @ORM\Table(name="event_type_factories", uniqueConstraints={
 *   @ORM\UniqueConstraint(name="eventtype_name_unique", columns={"eventType_id", "name"})
 * })
 */
class EventTypeFactory extends BaseEntity
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    private $factoryName;

    /**
     * @var EventType
     * @ORM\ManyToOne(targetEntity="EventType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eventType;


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }


    /**
     * @return string
     */
    public function getFactoryName(): string
    {
        return $this->factoryName;
    }

    public function setFactoryName(string $factoryName): void
    {
        $this->factoryName = $factoryName;
    }


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
}
