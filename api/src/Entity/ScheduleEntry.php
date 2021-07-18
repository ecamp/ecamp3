<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Validator\AssertBelongsToSameCamp;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A calendar event in a period of the camp, at which some activity will take place. The start time
 * is specified as an offset in minutes from the period's start time.
 *
 * @ORM\Entity
 */
#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: [
        'get',
        'patch' => ['denormalization_context' => [
            'groups' => ['scheduleEntry:update'],
            'allow_extra_attributes' => false,
        ]],
        'delete',
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['period', 'activity'])]
class ScheduleEntry extends BaseEntity implements BelongsToCampInterface {
    /**
     * The time period which this schedule entry is part of. Must belong to the same camp as the activity.
     *
     * @ORM\ManyToOne(targetEntity="Period", inversedBy="scheduleEntries")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    #[AssertBelongsToSameCamp]
    #[ApiProperty(example: '/periods/1a2b3c4d')]
    #[Groups(['Default', 'scheduleEntry:update'])]
    public ?Period $period = null;

    /**
     * The activity that will take place at the time defined by this schedule entry. Can not be changed
     * once the schedule entry is created.
     *
     * @internal Do not set the {@see Activity} directly on the ScheduleEntry. Instead use {@see Activity::addScheduleEntry()}
     * @ORM\ManyToOne(targetEntity="Activity", inversedBy="scheduleEntries")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    #[ApiProperty(example: '/activities/1a2b3c4d')]
    #[Groups(['Default'])]
    public ?Activity $activity = null;

    /**
     * The number of minutes that have passed since the start of the period when this schedule entry
     * starts.
     *
     * @var int minutes since period start
     * @ORM\Column(type="integer", nullable=false)
     */
    #[Assert\GreaterThanOrEqual(value: 0)]
    #[ApiProperty(example: 1440)]
    #[Groups(['Default', 'scheduleEntry:update'])]
    public int $periodOffset = 0;

    /**
     * The duration in minutes that this schedule entry will take.
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    #[Assert\GreaterThan(value: 0)]
    #[ApiProperty(example: 90)]
    #[Groups(['Default', 'scheduleEntry:update'])]
    public int $length = 0;

    /**
     * When rendering a period in a calendar view: Specifies how far offset the rendered calendar event
     * should be from the left border of the day column, as a fractional amount of the width of the whole
     * day. This is useful to arrange multiple overlapping schedule entries such that all of them are
     * visible. Should be a decimal number between 0 and 1, and left+width should not exceed 1, but the
     * API currently does not enforce this.
     *
     * @ORM\Column(name="`left`", type="float", nullable=true)
     * --> left is a MariaDB keyword, therefore escaping for column name necessary
     */
    #[ApiProperty(default: 0, example: 0.6)]
    #[Groups(['Default', 'scheduleEntry:update'])]
    public ?float $left = 0;

    /**
     * When rendering a period in a calendar view: Specifies how wide the rendered calendar event should
     * be, as a fractional amount of the width of the whole day. This is useful to arrange multiple
     * overlapping schedule entries such that all of them are visible. Should be a decimal number
     * between 0 and 1, and left+width should not exceed 1, but the API currently does not enforce this.
     *
     * @ORM\Column(type="float", nullable=true)
     */
    #[ApiProperty(example: 0.4)]
    #[Groups(['Default', 'scheduleEntry:update'])]
    public ?float $width = 1;

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this->activity?->camp;
    }

    /**
     * The day on which this schedule entry starts.
     */
    #[ApiProperty(writable: false, example: '/days/1a2b3c4d')]
    public function getDay(): Day {
        $dayNumber = $this->getDayNumber();

        return $this->period->days->filter(function (Day $day) use ($dayNumber) {
            return $day->getDayNumber() === $dayNumber;
        })->first();
    }

    #[ApiProperty(readable: false)]
    public function getNumberingStyle(): ?string {
        return $this->activity?->category?->numberingStyle;
    }

    /**
     * The day number of the day on which this schedule entry starts.
     */
    #[ApiProperty(example: '1')]
    public function getDayNumber(): int {
        return 1 + floor($this->periodOffset / (24 * 60));
    }

    /**
     * The cardinal number of this schedule entry, when chronologically ordering all
     * schedule entries that start on the same day. I.e. if the schedule entry is the
     * second entry on a given day, its number will be 2.
     */
    #[ApiProperty(example: '2')]
    public function getScheduleEntryNumber(): int {
        $dayOffset = floor($this->periodOffset / (24 * 60)) * 24 * 60;

        $expr = Criteria::expr();
        $crit = Criteria::create();
        $crit->where($expr->andX(
            $expr->gte('periodOffset', $dayOffset),
            $expr->lte('periodOffset', $this->periodOffset)
        ));

        /** @var Selectable $scheduleEntriesCollection */
        $scheduleEntriesCollection = $this->period->scheduleEntries;
        $scheduleEntries = $scheduleEntriesCollection->matching($crit);
        $activityNumber = $scheduleEntries->filter(function (ScheduleEntry $scheduleEntry) {
            if ($scheduleEntry->getNumberingStyle() === $this->getNumberingStyle()) {
                if ($scheduleEntry->periodOffset < $this->periodOffset) {
                    return true;
                }

                // left ScheduleEntry gets lower number
                $seLeft = $scheduleEntry->left;
                $thisLeft = $this->left;

                if ($seLeft < $thisLeft) {
                    return true;
                }
                if ($seLeft === $thisLeft) {
                    if ($scheduleEntry->createTime < $this->createTime) {
                        return true;
                    }
                }
            }

            return false;
        })->count();

        return $activityNumber + 1;
    }

    /**
     * Uniquely identifies this schedule entry in the period. This uses the day number, followed
     * by a period, followed by the cardinal number of the schedule entry in the numbering scheme
     * defined by the activity's category.
     */
    #[ApiProperty(example: '1.b')]
    public function getNumber(): string {
        $dayNumber = $this->getDayNumber();
        $scheduleEntryNumber = $this->getScheduleEntryNumber();

        $scheduleEntryStyledNumber = $this->activity?->category?->getStyledNumber($scheduleEntryNumber) ?? $scheduleEntryNumber;

        return $dayNumber.'.'.$scheduleEntryStyledNumber;
    }
}
