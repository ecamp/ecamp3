<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="periods")
 */
class Period extends BaseEntity {
    public function __construct() {
        parent::__construct();

        $this->days = new ArrayCollection();
        $this->eventInstances = new ArrayCollection();
    }


    /**
     * @var Camp
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $camp;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=false )
     */
    private $start;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=false )
     */
    private $end;

    /**
     * @var string
     * @ORM\Column(type="string", length=128, nullable=true )
     */
    private $description;

    /**
     * @var Day[]
     * @ORM\OneToMany(targetEntity="Day", mappedBy="period", orphanRemoval=true)
     * @ORM\OrderBy({"dayOffset" = "ASC"})
     */
    protected $days;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="EventInstance", mappedBy="period", cascade={"all"}, orphanRemoval=true)
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
     * @return \DateTime
     */
    public function getStart() {
        if ($this->start !== null) {
            $start = (clone $this->start);
            $start->setTime(0, 0, 0);
            return $start;
        }
        return null;
    }

    public function setStart(\DateTime $start): void {
        $start = clone $start;
        $start->setTime(0, 0, 0);

        $this->start = $start;

        if ($this->end != null && $this->end < $start) {
            $this->setEnd($start);
        }
    }


    /**
     * @return \DateTime
     */
    public function getEnd() {
        if ($this->end !== null) {
            $end = clone $this->end;
            $end->setTime(23, 59, 59);
            return $end;
        }
        return null;
    }

    public function setEnd(\DateTime $end): void {
        $end = clone $end;
        $end->setTime(23, 59, 59);

        $this->end = $end;

        if ($this->start != null && $this->start > $end) {
            $this->setStart($end);
        }
    }


    /**
     * @return int
     */
    public function getDurationInDays(): int {
        $start = $this->getStart();
        $end = $this->getEnd();

        if ($start !== null && $end !== null) {
            $diff = $end->getTimestamp() - $start->getTimestamp();
            return ceil($diff / 86400);
        }
        return 0;
    }


    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description): void {
        $this->description = $description;
    }


    /**
     * @return ArrayCollection
     */
    public function getDays() {
        return $this->days;
    }

    public function addDay(Day $day): void {
        $day->setPeriod($this);
        $this->days->add($day);
    }

    public function removeDay(Day $day): void {
        $day->setPeriod(null);
        $this->days->removeElement($day);
    }


    /**
     * @return ArrayCollection
     */
    public function getEventInstances() {
        return $this->eventInstances;
    }

    public function addEventInstance(EventInstance $eventInstance) {
        $eventInstance->setPeriod($this);
        $this->eventInstances->add($eventInstance);
    }

    public function removeEventInstance(EventInstance $eventInstance) {
        $eventInstance->setPeriod(null);
        $this->eventInstances->removeElement($eventInstance);
    }



    /** @ORM\PrePersist */
    public function PrePersist() {
        parent::PrePersist();

        // Update Number of days
    }

    /** @ORM\PreUpdate */
    public function PreUpdate() {
        parent::PreUpdate();

        // Update Number of days
    }
}
