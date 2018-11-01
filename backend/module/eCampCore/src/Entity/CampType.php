<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;
use Zend\Json\Json;

/**
 * CampType
 * @ORM\Entity
 * @ORM\Table(name="camp_types")
 */
class CampType extends BaseEntity {
    const CNF_EVENT_CATEGORIES  = 'event_categories';
    const CNF_JOBS              = 'jobs';

    public function __construct() {
        parent::__construct();

        $this->eventTypes = new ArrayCollection();
    }

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var Organization
     * @ORM\ManyToOne(targetEntity="Organization")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $organization;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isJS;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isCourse;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $jsonConfig;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="EventType")
     * @ORM\JoinTable(name="camp_type_event_type",
     *      joinColumns={@ORM\JoinColumn(name="camptype_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="eventtype_id", referencedColumnName="id")}
     * )
     */
    private $eventTypes;


    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    public function setName(string $name) {
        $this->name = $name;
    }


    /**
     * @return Organization
     */
    public function getOrganization() {
        return $this->organization;
    }

    public function setOrganization($organization) {
        $this->organization = $organization;
    }


    /**
     * @return bool
     */
    public function getIsJS() {
        return $this->isJS;
    }

    public function setIsJS(bool $isJS) {
        $this->isJS = $isJS;
    }


    /**
     * @return bool
     */
    public function getIsCourse() {
        return $this->isCourse;
    }

    public function setIsCourse(bool $isCourse) {
        $this->isCourse = $isCourse;
    }


    /**
     * @return string
     */
    public function getJsonConfig(): string {
        return $this->jsonConfig;
    }

    public function setJsonConfig(string $jsonConfig): void {
        $this->jsonConfig = $jsonConfig;
    }

    /**
     * @param string $key
     * @return object
     */
    public function getConfig($key = null) {
        $config = null;
        if ($this->jsonConfig != null) {
            $config = Json::decode($this->jsonConfig);
            if ($key != null) {
                if (isset($config->{$key})) {
                    $config = $config->{$key};
                } else {
                    $config = null;
                }
            }
        }
        return $config;
    }


    /**
     * @return ArrayCollection
     */
    public function getEventTypes() {
        return $this->eventTypes;
    }

    public function addEventType(EventType $eventType) {
        $this->eventTypes->add($eventType);
    }

    public function removeEventType(EventType $eventType) {
        $this->eventTypes->removeElement($eventType);
    }
}
