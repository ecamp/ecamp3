<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\Collection;
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
     * @ORM\OneToMany(targetEntity="DayResponsible", mappedBy="day", orphanRemoval=true)
     */
    protected Collection $dayResponsibles;

    /**
     * @ORM\ManyToOne(targetEntity="Period")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private ?Period $period = null;

    /**
     * @ORM\Column(type="integer")
     */
    private int $dayOffset = 0;

    public function getPeriod(): ?Period {
        return $this->period;
    }

    public function setPeriod(?Period $period): void {
        $this->period = $period;
    }

    public function getCamp(): ?Camp {
        return (null != $this->period) ? $this->period->getCamp() : null;
    }

    public function getDayOffset(): int {
        return $this->dayOffset;
    }

    public function setDayOffset(int $dayOffset): void {
        $this->dayOffset = $dayOffset;
    }

    public function getDayNumber(): int {
        return $this->dayOffset + 1;
    }

    /**
     * Returns all scheduleEntries which start on the current day (using midnight as cut-time).
     */
    public function getScheduleEntries(): Collection {
        $dayOffset = $this->getDayOffset();

        return $this->period->getScheduleEntries()->filter(
            // filters all scheduleEntries which start on the current day
            function (ScheduleEntry $scheduleEntry) use ($dayOffset) {
                return $scheduleEntry->getPeriodOffset() >= $dayOffset * 24 * 60              // after midnight of current day
                        && $scheduleEntry->getPeriodOffset() < ($dayOffset + 1) * 24 * 60;    // before midnight of next day
            }
        );
    }

    public function getDayResponsibles(): Collection {
        return $this->dayResponsibles;
    }

    public function addDayResponsible(DayResponsible $dayResponsible): void {
        $dayResponsible->setDay($this);
        $this->dayResponsibles->add($dayResponsible);
    }

    public function removeDayResponsible(DayResponsible $dayResponsible): void {
        $dayResponsible->setDay(null);
        $this->dayResponsibles->removeElement($dayResponsible);
    }
}
