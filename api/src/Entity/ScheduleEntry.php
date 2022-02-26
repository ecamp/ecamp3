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
use DateInterval;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Mapping as ORM;
use RuntimeException;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A calendar event in a period of the camp, at which some activity will take place. The start time
 * is specified as an offset in minutes from the period's start time.
 *
 * @ORM\Entity(repositoryClass=ScheduleEntryRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => 'is_authenticated()'],
        'post' => [
            'denormalization_context' => ['groups' => ['write', 'create']],
            'normalization_context' => self::ITEM_NORMALIZATION_CONTEXT,
            'security_post_denormalize' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
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
class ScheduleEntry extends BaseEntity implements BelongsToCampInterface {
    public const ITEM_NORMALIZATION_CONTEXT = [
        'groups' => ['read', 'ScheduleEntry:Activity'],
        'swagger_definition_name' => 'read',
    ];

    /**
     * The time period which this schedule entry is part of. Must belong to the same camp as the activity.
     *
     * @ORM\ManyToOne(targetEntity="Period", inversedBy="scheduleEntries")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    #[AssertBelongsToSameCamp]
    #[ApiProperty(example: '/periods/1a2b3c4d')]
    #[Groups(['read', 'write'])]
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
    #[Groups(['read', 'create'])]
    public ?Activity $activity = null;

    /**
     * The offset in minutes between start of the period and start of this scheduleEntry.
     * This property is not exposed via API (use `start` instead).
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    #[Assert\GreaterThanOrEqual(value: 0)]
    public int $startOffset = 0;

    /**
     * The offset in minutes between start of the period and end of this scheduleEntry.
     * This property is not exposed via API (use `end` instead).
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    #[Assert\GreaterThan(propertyPath: 'startOffset')]
    public int $endOffset = 60;

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
    #[Groups(['read', 'write'])]
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
    #[Groups(['read', 'write'])]
    public ?float $width = 1;

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this->activity?->getCamp();
    }

    /**
     * Start date and time of the schedule entry.
     */
    #[ApiProperty(example: '2022-01-02T00:00:00+00:00', required: true, openapiContext: ['format' => 'date-time'])]
    #[Groups(['read'])]
    public function getStart(): ?DateTime {
        try {
            $start = $this->period?->start ? DateTime::createFromInterface($this->period->start) : null;
            $start?->add(new DateInterval('PT'.$this->startOffset.'M'));

            return $start;
        } catch (\Exception $e) {
            return null;
        }
    }

    #[Groups(['write'])]
    public function setStart(DateTimeInterface $start): void {
        if (null !== $this->period?->start) {
            $this->startOffset = DateTimeUtil::differenceInMinutes($this->period->start, $start);
        }
    }

    /**
     * End date and time of the schedule entry.
     */
    #[ApiProperty(example: '2022-01-02T01:30:00+00:00', required: true, openapiContext: ['format' => 'date-time'])]
    #[Groups(['read'])]
    public function getEnd(): ?DateTime {
        try {
            $end = $this->period?->start ? DateTime::createFromInterface($this->period->start) : null;
            $end?->add(new DateInterval('PT'.$this->endOffset.'M'));

            return $end;
        } catch (\Exception $e) {
            return null;
        }
    }

    #[Groups(['write'])]
    public function setEnd(DateTimeInterface $end): void {
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
            throw new RuntimeException("Could not find Day entity for dayOffset {$dayOffset}");
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
        $dayOffset = floor($this->startOffset / (24 * 60)) * 24 * 60;

        $expr = Criteria::expr();
        $crit = Criteria::create();
        $crit->where($expr->andX(
            $expr->neq('id', $this->getId()),
            $expr->gte('startOffset', $dayOffset),
            $expr->lte('startOffset', $this->startOffset)
        ));

        /** @var Selectable $scheduleEntriesCollection */
        $scheduleEntriesCollection = $this->period->scheduleEntries;
        $scheduleEntries = $scheduleEntriesCollection->matching($crit);

        return 1 + $scheduleEntries->filter(function (ScheduleEntry $scheduleEntry) {
            if ($scheduleEntry->getNumberingStyle() !== $this->getNumberingStyle()) {
                return false;
            }
            if ($scheduleEntry->startOffset < $this->startOffset) {
                return true;
            }
            if ($scheduleEntry->left < $this->left) {
                return true;
            }
            if ($scheduleEntry->left === $this->left) {
                if ($scheduleEntry->endOffset > $this->endOffset) {
                    return true;
                }
                if ($scheduleEntry->endOffset === $this->endOffset) {
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
