<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Doctrine\Filter\ExpressionDateTimeFilter;
use App\Repository\ScheduleEntryRepository;
use App\Util\DateTimeUtil;
use App\Validator\AssertBelongsToSameCamp;
use App\Validator\ScheduleEntryPostGroupSequence;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A calendar event in a period of the camp, at which some activity will take place. The start time
 * is specified as an offset in minutes from the period's start time.
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => 'is_authenticated()'],
        'post' => [
            'denormalization_context' => ['groups' => ['write', 'create']],
            'normalization_context' => self::ITEM_NORMALIZATION_CONTEXT,
            'security_post_denormalize' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            'validation_groups' => ScheduleEntryPostGroupSequence::class,
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => self::ITEM_NORMALIZATION_CONTEXT,
            'security' => 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)',
        ],
        'patch' => [
            'normalization_context' => self::ITEM_NORMALIZATION_CONTEXT,
            'security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
        ],
        'delete' => ['security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'],
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
#[ApiFilter(SearchFilter::class, properties: ['period', 'activity'])]
#[ApiFilter(ExpressionDateTimeFilter::class, properties: [
    'start' => 'DATE_ADD({period.start}, {}.startOffset, \'minute\')',
    'end' => 'DATE_ADD({period.start}, {}.endOffset, \'minute\')',
])]
#[ORM\Entity(repositoryClass: ScheduleEntryRepository::class)]
#[ORM\Index(columns: ['startOffset'])]
#[ORM\Index(columns: ['endOffset'])]
class ScheduleEntry extends BaseEntity implements BelongsToCampInterface {
    public const ITEM_NORMALIZATION_CONTEXT = [
        'groups' => ['read', 'ScheduleEntry:Activity'],
        'swagger_definition_name' => 'read',
    ];

    /**
     * The time period which this schedule entry is part of. Must belong to the same camp as the activity.
     */
    #[Assert\NotNull(groups: ['validPeriod'])] // this is validated before all others
    #[AssertBelongsToSameCamp]
    #[ApiProperty(example: '/periods/1a2b3c4d')]
    #[Groups(['read', 'write'])]
    #[ORM\ManyToOne(targetEntity: Period::class, inversedBy: 'scheduleEntries', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?Period $period = null;

    /**
     * The activity that will take place at the time defined by this schedule entry. Can not be changed
     * once the schedule entry is created.
     *
     * @internal Do not set the {@see Activity} directly on the ScheduleEntry. Instead use {@see Activity::addScheduleEntry()}
     */
    #[ApiProperty(example: '/activities/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    #[ORM\ManyToOne(targetEntity: Activity::class, inversedBy: 'scheduleEntries')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?Activity $activity = null;

    /**
     * The offset in minutes between start of the period and start of this scheduleEntry.
     * This property is not exposed via API (use `start` instead).
     */
    #[ORM\Column(type: 'integer', nullable: false)]
    public int $startOffset = 0;

    /**
     * The offset in minutes between start of the period and end of this scheduleEntry.
     * This property is not exposed via API (use `end` instead).
     */
    #[ORM\Column(type: 'integer', nullable: false)]
    public int $endOffset = 60;

    /**
     * When rendering a period in a calendar view: Specifies how far offset the rendered calendar event
     * should be from the left border of the day column, as a fractional amount of the width of the whole
     * day. This is useful to arrange multiple overlapping schedule entries such that all of them are
     * visible. Should be a decimal number between 0 and 1, and left+width should not exceed 1, but the
     * API currently does not enforce this.
     */
    #[ApiProperty(default: 0, example: 0.6)]
    #[Groups(['read', 'write'])]
    #[ORM\Column(name: '`left`', type: 'float', nullable: true)]
    public ?float $left = 0;

    /**
     * When rendering a period in a calendar view: Specifies how wide the rendered calendar event should
     * be, as a fractional amount of the width of the whole day. This is useful to arrange multiple
     * overlapping schedule entries such that all of them are visible. Should be a decimal number
     * between 0 and 1, and left+width should not exceed 1, but the API currently does not enforce this.
     */
    #[ApiProperty(example: 0.4)]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'float', nullable: true)]
    public ?float $width = 1;

    /**
     * internal cache of 'start' and 'end' property during denormalization
     * this is necessary in case period is denormalized after 'start' or 'end'.
     */
    private ?DateTimeInterface $_start = null;
    private ?DateTimeInterface $_end = null;

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this->activity?->getCamp();
    }

    public function getPeriod(): Period|null {
        return $this->period;
    }

    public function setPeriod(Period $period): void {
        $this->period = $period;

        // if start has been denormalized, calculate startOffset
        if (null !== $this->_start) {
            $this->setStart($this->_start);
        }
        // if end has been denormalized, calculate endOffset
        if (null !== $this->_end) {
            $this->setEnd($this->_end);
        }
    }

    /**
     * Start date and time of the schedule entry.
     */
    #[ApiProperty(example: '2022-01-02T00:00:00+00:00', required: true, openapiContext: ['format' => 'date-time'])]
    #[Assert\GreaterThanOrEqual(propertyPath: 'period.start')]
    #[Groups(['read'])]
    public function getStart(): ?DateTimeInterface {
        if (null === $this->period?->start) {
            return $this->_start;
        }

        $start = DateTime::createFromInterface($this->period->start);
        $start->modify("{$this->startOffset} minutes");

        return $start;
    }

    #[Groups(['write'])]
    public function setStart(DateTimeInterface $start): void {
        $this->_start = $start;

        if (null !== $this->period?->start) {
            $this->startOffset = DateTimeUtil::differenceInMinutes($this->period->start, $start);
        }
    }

    /**
     * End date and time of the schedule entry.
     */
    #[ApiProperty(example: '2022-01-02T01:30:00+00:00', required: true, openapiContext: ['format' => 'date-time'])]
    #[Assert\GreaterThan(propertyPath: 'start')]
    #[Assert\LessThanOrEqual(propertyPath: 'period.endOfLastDay')]
    #[Groups(['read'])]
    public function getEnd(): ?DateTimeInterface {
        if (null === $this->period?->start) {
            return $this->_end;
        }

        $end = DateTime::createFromInterface($this->period->start);
        $end->modify("{$this->endOffset} minutes");

        return $end;
    }

    #[Groups(['write'])]
    public function setEnd(DateTimeInterface $end): void {
        $this->_end = $end;

        if (null !== $this->period?->start) {
            $this->endOffset = DateTimeUtil::differenceInMinutes($this->period->start, $end);
        }
    }

    /**
     * @return Activity
     */
    #[ApiProperty(readableLink: true)]
    #[SerializedName('activity')]
    #[Groups('ScheduleEntry:Activity')]
    public function getEmbeddedActivity(): ?Activity {
        return $this->activity;
    }

    /**
     * The day on which this schedule entry starts.
     */
    #[ApiProperty(writable: false, example: '/days/1a2b3c4d')]
    #[Groups(['read'])]
    public function getDay(): Day|null {
        $dayOffset = $this->getDayOffset();

        $filteredDays = $this->period->days->filter(function (Day $day) use ($dayOffset) {
            return $day->dayOffset === $dayOffset;
        });

        if ($filteredDays->isEmpty()) {
            return null;
        }

        return $filteredDays->first();
    }

    #[ApiProperty(readable: false)]
    public function getNumberingStyle(): ?string {
        return $this->activity?->category?->numberingStyle;
    }

    /**
     * The day number of the day on which this schedule entry starts.
     */
    #[ApiProperty(example: '1')]
    #[Groups(['read'])]
    public function getDayNumber(): int {
        return $this->period->getFirstDayNumber() + $this->getDayOffset();
    }

    /**
     * The cardinal number of this schedule entry, when chronologically ordering all
     * schedule entries that start on the same day. I.e. if the schedule entry is the
     * second entry on a given day, its number will be 2.
     */
    #[ApiProperty(example: '2')]
    #[Groups(['read'])]
    public function getScheduleEntryNumber(): int {
        $dayOffsetInMinutes = $this->getDayOffset() * 24 * 60;

        return 1 + $this->period->scheduleEntries->filter(function (ScheduleEntry $scheduleEntry) use ($dayOffsetInMinutes) {
            // don't count self
            if ($scheduleEntry->getId() === $this->getId()) {
                return false;
            }

            // don't count scheduleEntries of previous days
            if ($scheduleEntry->startOffset < $dayOffsetInMinutes) {
                return false;
            }

            // don't count scheduleEntries starting later
            if ($scheduleEntry->startOffset > $this->startOffset) {
                return false;
            }

            // don't count scheduleEntries of different numbering styles
            if ($scheduleEntry->getNumberingStyle() !== $this->getNumberingStyle()) {
                return false;
            }

            // count scheduleEntries starting earlier
            if ($scheduleEntry->startOffset < $this->startOffset) {
                return true;
            }

            // for scheduleEntries starting at same time: count lower 'left'
            if ($scheduleEntry->left < $this->left) {
                return true;
            }

            if ($scheduleEntry->left === $this->left) {
                // for scheduleEntries starting at same time & same left value: count longer entries
                if ($scheduleEntry->endOffset > $this->endOffset) {
                    return true;
                }
                if ($scheduleEntry->endOffset === $this->endOffset) {
                    // for scheduleEntries starting at same time & same left value & same duration: ID as fallback comparison
                    if ($scheduleEntry->getId() < $this->getId()) {
                        return true;
                    }
                }
            }

            return false;
        })->count();
    }

    /**
     * Uniquely identifies this schedule entry in the period. This uses the day number, followed
     * by a period, followed by the cardinal number of the schedule entry in the numbering scheme
     * defined by the activity's category.
     */
    #[ApiProperty(example: '1.b')]
    #[Groups(['read'])]
    public function getNumber(): string {
        $dayNumber = $this->getDayNumber();
        $scheduleEntryNumber = $this->getScheduleEntryNumber();

        $scheduleEntryStyledNumber = $this->activity?->category?->getStyledNumber($scheduleEntryNumber) ?? $scheduleEntryNumber;

        return $dayNumber.'.'.$scheduleEntryStyledNumber;
    }

    /**
     * The dayOffset within the period (zero-based)
     * First day has an offset of zero.
     */
    private function getDayOffset(): int {
        return (int) floor($this->startOffset / (24 * 60));
    }
}
