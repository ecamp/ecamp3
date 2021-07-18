<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * A day in a time period of a camp. This is represented as a reference to the time period
 * along with a number of days offset from the period's starting date. This is to make it
 * easier to move the whole periods to different dates. Days are created automatically when
 * creating or updating periods, and are not writable through the API directly.
 *
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={
 *     @ORM\UniqueConstraint(name="offset_period_idx", columns={"periodId", "dayOffset"})
 * })
 */
#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get'],
)]
#[ApiFilter(SearchFilter::class, properties: ['period'])]
#[UniqueEntity(fields: ['period', 'dayOffset'])]
class Day extends BaseEntity implements BelongsToCampInterface {
    /**
     * The list of people who have a whole-day responsibility on this day.
     *
     * @ORM\OneToMany(targetEntity="DayResponsible", mappedBy="day", orphanRemoval=true)
     */
    #[ApiProperty(writable: false, example: '["/day_responsibles/1a2b3c4d"]')]
    public Collection $dayResponsibles;

    /**
     * The time period that this day belongs to.
     *
     * @ORM\ManyToOne(targetEntity="Period", inversedBy="days")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    #[ApiProperty(writable: false, example: '/periods/1a2b3c4d')]
    public ?Period $period = null;

    /**
     * The 0-based offset in days from the period's start date when this day starts.
     *
     * @ORM\Column(type="integer")
     */
    #[ApiProperty(writable: false, example: '1')]
    public int $dayOffset = 0;

    public function __construct() {
        $this->dayResponsibles = new ArrayCollection();
    }

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this->period?->camp;
    }

    /**
     * The 1-based cardinal number of the day in the period. Not unique within the camp.
     */
    #[ApiProperty(example: '2')]
    #[SerializedName('number')]
    public function getDayNumber(): int {
        return $this->dayOffset + 1;
    }

    /**
     * All scheduleEntries which start on the current day (using midnight as cutoff).
     *
     * @return ScheduleEntry[]
     */
    #[ApiProperty(example: '["/schedule_entries/1a2b3c4d"]')]
    public function getScheduleEntries(): array {
        $dayOffset = $this->dayOffset;

        return array_filter(
            $this->period->getScheduleEntries(),
            // filters all scheduleEntries which start on the current day
            function (ScheduleEntry $scheduleEntry) use ($dayOffset) {
                return $scheduleEntry->periodOffset >= $dayOffset * 24 * 60              // after midnight of current day
                        && $scheduleEntry->periodOffset < ($dayOffset + 1) * 24 * 60;    // before midnight of next day
            }
        );
    }

    /**
     * @return DayResponsible[]
     */
    public function getDayResponsibles(): array {
        return $this->dayResponsibles->getValues();
    }

    public function addDayResponsible(DayResponsible $dayResponsible): self {
        if (!$this->dayResponsibles->contains($dayResponsible)) {
            $this->dayResponsibles[] = $dayResponsible;
            $dayResponsible->day = $this;
        }

        return $this;
    }

    public function removeDayResponsible(DayResponsible $dayResponsible): self {
        if ($this->dayResponsibles->removeElement($dayResponsible)) {
            if ($dayResponsible->day === $this) {
                $dayResponsible->day = null;
            }
        }

        return $this;
    }
}
