<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
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
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
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
