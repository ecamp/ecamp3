<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="offset_period_idx", columns={"periodId", "dayOffset"})
 * })
 * @ORM\HasLifecycleCallbacks
 */
#[ApiResource]
class Day extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\OneToMany(targetEntity="DayResponsible", mappedBy="day", orphanRemoval=true)
     *
     * @var DayResponsible[]
     */
    public $dayResponsibles;

    /**
     * @ORM\ManyToOne(targetEntity="Period", inversedBy="days")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    public ?Period $period = null;

    /**
     * @ORM\Column(type="integer")
     */
    public int $dayOffset = 0;

    public function getPeriod(): ?Period {
        return $this->period;
    }

    public function setPeriod(?Period $period): void {
        $this->period = $period;
    }

    public function getCamp(): ?Camp {
        return (null != $this->period) ? $this->period->camp : null;
    }

    public function getDayNumber(): int {
        return $this->dayOffset + 1;
    }

    /**
     * Returns all scheduleEntries which start on the current day (using midnight as cut-time).
     */
    public function getScheduleEntries(): Collection {
        $dayOffset = $this->dayOffset;

        return $this->period->getScheduleEntries()->filter(
            // filters all scheduleEntries which start on the current day
            function (ScheduleEntry $scheduleEntry) use ($dayOffset) {
                return $scheduleEntry->periodOffset >= $dayOffset * 24 * 60              // after midnight of current day
                        && $scheduleEntry->periodOffset < ($dayOffset + 1) * 24 * 60;    // before midnight of next day
            }
        );
    }

    public function getDayResponsibles(): array {
        return $this->dayResponsibles->getValues();
    }

    public function addDayResponsible(DayResponsible $dayResponsible): void {
        $dayResponsible->day = $this;
        $this->dayResponsibles->add($dayResponsible);
    }

    public function removeDayResponsible(DayResponsible $dayResponsible): void {
        $dayResponsible->day = null;
        $this->dayResponsibles->removeElement($dayResponsible);
    }
}
