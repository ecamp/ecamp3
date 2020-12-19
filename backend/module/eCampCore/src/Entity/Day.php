<?php

namespace eCamp\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="offset_period_idx", columns={"periodId", "dayOffset"})
 * })
 * @ORM\HasLifecycleCallbacks
 */
class Day extends BaseEntity implements BelongsToCampInterface {
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

    public function __construct() {
        parent::__construct();
    }

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
        return (null != $this->period) ? $this->period->getCamp() : null;
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
        return $this->dayOffset + 1;
    }

    /**
     * Returns all scheduleEntries which start on the current day (using midnight as cut-time).
     *
     * @return ArrayCollection
     */
    public function getScheduleEntries() {
        $dayOffset = $this->getDayOffset();

        return $this->period->getScheduleEntries()->filter(
            // filters all scheduleEntries which start on the current day
            function (ScheduleEntry $scheduleEntry) use ($dayOffset) {
                return $scheduleEntry->getPeriodOffset() >= $dayOffset * 24 * 60              // after midnight of current day
                        && $scheduleEntry->getPeriodOffset() < ($dayOffset + 1) * 24 * 60;    // before midnight of next day
            }
        );
    }
}
