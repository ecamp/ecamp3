<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="days", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="offset_period_idx", columns={"period_id", "dayOffset"})
 * })
 * @ORM\HasLifecycleCallbacks
 */
class Day extends BaseEntity {
    public function __construct() {
        parent::__construct();
    }


    /**
     * @var Period
     * @ORM\ManyToOne(targetEntity="Period")
     * @ORM\JoinColumn(nullable=false)
     */
    private $period;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $dayOffset;


    /**
     * @return Period
     */
    public function getPeriod() {
        return $this->period;
    }

    public function setPeriod($period) {
        $this->period = $period;
    }


    /**
     * @return Camp
     */
    public function getCamp() {
        return ($this->period != null) ? $this->period->getCamp() : null;
    }

    /**
     * @return ArrayCollection
     */
    public function getEventInstances() {
        $dayStart = 24 * 60 * ($this->dayOffset);
        $dayEnd = 24 * 60 * ($this->dayOffset + 1);

        $eventInstances = $this->period->getEventInstances();
        $eventInstances = $eventInstances->filter(
            function($ei) use ($dayStart, $dayEnd) {
                /** @var EventInstance $ei */
                $start = $ei->getStart();
                $end = $start + $ei->getLength();

                if ($start > $dayEnd) return false;
                if ($end < $dayStart) return false;
                return true;
            }
        );

        return $eventInstances;
    }

    /**
     * @return int
     */
    public function getDayOffset() {
        return $this->dayOffset;
    }

    public function setDayOffset(int $dayOffset) {
        $this->dayOffset = $dayOffset;
    }

    public function getDayNumber() {
        return ($this->dayOffset + 1);
    }
}
