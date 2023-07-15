<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\InputFilter;
use App\Repository\PeriodRepository;
use App\Serializer\Normalizer\RelatedCollectionLink;
use App\State\PeriodPersistProcessor;
use App\Validator\Period\AssertGreaterThanOrEqualToLastScheduleEntryEnd;
use App\Validator\Period\AssertLessThanOrEqualToEarliestScheduleEntryStart;
use App\Validator\Period\AssertNotOverlappingWithOtherPeriods;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A time period in which the programme of a camp will take place. There may be multiple
 * periods in a camp, but they may not overlap. A period is made up of one or more full days.
 */
#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)',
            normalizationContext: self::ITEM_NORMALIZATION_CONTEXT,
        ),
        new Patch(
            processor: PeriodPersistProcessor::class,
            security: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'
        ),
        new Delete(
            security: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
            validationContext: ['groups' => ['delete', 'Period:delete']]
        ),
        new GetCollection(
            security: 'is_authenticated()'
        ),
        new Post(
            processor: PeriodPersistProcessor::class,
            denormalizationContext: ['groups' => ['write', 'create']],
            securityPostDenormalize: 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'
        ),
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']]
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['camp'])]
#[ORM\Entity(repositoryClass: PeriodRepository::class)]
class Period extends BaseEntity implements BelongsToCampInterface {
    public const ITEM_NORMALIZATION_CONTEXT = [
        'groups' => ['read', 'Period:Camp', 'Period:Days'],
        'swagger_definition_name' => 'read',
    ];

    /**
     * The days in this time period. These are generated automatically.
     */
    #[ApiProperty(writable: false, example: '["/days?period=/periods/1a2b3c4d"]')]
    #[Groups(['read'])]
    #[ORM\OneToMany(targetEntity: Day::class, mappedBy: 'period', orphanRemoval: true, cascade: ['persist'])]
    #[ORM\OrderBy(['dayOffset' => 'ASC'])]
    public Collection $days;

    /**
     * All time slots for programme that are part of this time period. A schedule entry
     * may span over multiple days, but may not end later than the period.
     *
     * @var Collection<int, ScheduleEntry>
     */
    #[ApiProperty(writable: false, example: '["/schedule_entries/1a2b3c4d"]')]
    #[Groups(['read'])]
    #[ORM\OneToMany(targetEntity: ScheduleEntry::class, mappedBy: 'period')]
    #[ORM\OrderBy(['startOffset' => 'ASC', 'left' => 'ASC', 'endOffset' => 'DESC', 'id' => 'ASC'])]
    public Collection $scheduleEntries;

    /**
     * Material items that are assigned directly to the period, as opposed to individual
     * activities.
     */
    #[ORM\OneToMany(targetEntity: MaterialItem::class, mappedBy: 'period')]
    public Collection $materialItems;

    /**
     * The camp that this time period belongs to. Cannot be changed once the period is created.
     */
    #[Assert\Valid(groups: ['Period:delete'])]
    #[ApiProperty(example: '/camps/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    #[ORM\ManyToOne(targetEntity: Camp::class, inversedBy: 'periods')]
    #[ORM\JoinColumn(nullable: false)]
    public ?Camp $camp = null;

    /**
     * A free text name for the period. Useful to distinguish multiple periods in the same camp.
     *
     * TODO: Make non-nullable in the DB
     */
    #[InputFilter\CleanText]
    #[InputFilter\Trim]
    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    #[ApiProperty(example: 'Hauptlager')]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $description = null;

    /**
     * The day on which the period starts, as an ISO date string. Should not be after "end".
     */
    #[Assert\LessThanOrEqual(propertyPath: 'end')]
    #[AssertLessThanOrEqualToEarliestScheduleEntryStart()]
    #[AssertNotOverlappingWithOtherPeriods]
    #[ApiProperty(example: '2022-01-01', openapiContext: ['format' => 'date'])]
    #[Context(
        normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'],
        denormalizationContext: [DateTimeNormalizer::FORMAT_KEY => '!Y-m-d']
    )]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'date')]
    public ?\DateTimeInterface $start = null;

    /**
     * The (inclusive) day at the end of which the period ends, as an ISO date string. Should
     * not be before "start".
     */
    #[Assert\GreaterThanOrEqual(propertyPath: 'start')]
    #[AssertGreaterThanOrEqualToLastScheduleEntryEnd()]
    #[AssertNotOverlappingWithOtherPeriods]
    #[ApiProperty(example: '2022-01-08', openapiContext: ['format' => 'date'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => '!Y-m-d'])]
    #[Context(
        normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'],
        denormalizationContext: [DateTimeNormalizer::FORMAT_KEY => '!Y-m-d']
    )]
    #[Groups(['read', 'write'])]
    #[ORM\Column(name: '`end`', type: 'date')]
    public ?\DateTimeInterface $end = null;

    /**
     * If the start date of the period is changing, moveScheduleEntries defines what happens with the schedule
     * entries in the period.
     * true: The schedule entries will be moved together with the period (startOffset stays the same).
     * false: The start date of each schedule entry remains the same (startOffset changes).
     */
    #[ApiProperty(example: true)]
    #[Groups(['write'])]
    public bool $moveScheduleEntries = true;

    public function __construct() {
        parent::__construct();
        $this->days = new ArrayCollection();
        $this->scheduleEntries = new ArrayCollection();
        $this->materialItems = new ArrayCollection();
    }

    /**
     * @return Day[]
     */
    #[ApiProperty(readableLink: true)]
    #[SerializedName('days')]
    #[Groups('Period:Days')]
    public function getEmbeddedDays(): array {
        return $this->days->getValues();
    }

    #[ApiProperty(readableLink: true)]
    #[SerializedName('camp')]
    #[Groups(['Period:Camp'])]
    public function getEmbeddedCamp(): ?Camp {
        return $this->camp;
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    /**
     * @return Day[]
     */
    public function getDays(): array {
        return $this->days->getValues();
    }

    public function getDaysSorted(): array {
        $days = $this->getDays();
        usort($days, fn (Day $a, Day $b) => $a->dayOffset <=> $b->dayOffset);

        return $days;
    }

    public function addDay(Day $day): self {
        if (!$this->days->contains($day)) {
            $this->days[] = $day;
            $day->period = $this;
        }

        return $this;
    }

    public function removeDay(Day $day): self {
        if ($this->days->removeElement($day)) {
            if ($day->period === $this) {
                $day->period = null;
            }
        }

        return $this;
    }

    /**
     * @return ScheduleEntry[]
     */
    public function getScheduleEntries(): array {
        return $this->scheduleEntries->getValues();
    }

    public function addScheduleEntry(ScheduleEntry $scheduleEntry): self {
        if (!$this->scheduleEntries->contains($scheduleEntry)) {
            $this->scheduleEntries[] = $scheduleEntry;
            $scheduleEntry->period = $this;
        }

        return $this;
    }

    public function removeScheduleEntry(ScheduleEntry $scheduleEntry): self {
        if ($this->scheduleEntries->removeElement($scheduleEntry)) {
            if ($scheduleEntry->period === $this) {
                $scheduleEntry->period = null;
            }
        }

        return $this;
    }

    /**
     * All the content nodes used in some activity which is carried out (has a schedule entry) in this period.
     *
     * The list is anyway replaced by a RelatedCollectionLink, thus we don't need to fetch the data now.
     *
     * @return ContentNode[]
     */
    #[ApiProperty(writable: false, example: '["/content_nodes/1a2b3c4d"]')]
    #[RelatedCollectionLink(ContentNode::class, ['period' => '$this'])]
    #[Groups(['read'])]
    public function getContentNodes(): array {
        return [];
    }

    /**
     * A link to all the DayResponsibles in this period.
     * The list is anyway replaced by a RelatedCollectionLink, thus we don't need to fetch the data now.
     *
     * @return DayResponsible[]
     */
    #[ApiProperty(writable: false, example: '["/day_responsibles/1a2b3c4d"]')]
    #[RelatedCollectionLink(DayResponsible::class, ['day.period' => '$this'])]
    #[Groups(['read'])]
    public function getDayResponsibles(): array {
        return [];
    }

    /**
     * @return MaterialItem[]
     */
    #[ApiProperty(writable: false, example: '["/material_items/1a2b3c4d"]')]
    #[RelatedCollectionLink(MaterialItem::class, ['period' => '$this'])]
    #[Groups(['read'])]
    public function getMaterialItems(): array {
        return $this->materialItems->getValues();
    }

    public function addMaterialItem(MaterialItem $materialItem): self {
        if (!$this->materialItems->contains($materialItem)) {
            $this->materialItems[] = $materialItem;
            $materialItem->period = $this;
        }

        return $this;
    }

    public function removeMaterialItem(MaterialItem $materialItem): self {
        if ($this->materialItems->removeElement($materialItem)) {
            if ($materialItem->period === $this) {
                $materialItem->period = null;
            }
        }

        return $this;
    }

    /**
     * Returns the length of this period in days
     * (based on start and end date).
     */
    public function getPeriodLength(): ?int {
        if (isset($this->start, $this->end)) {
            $length = $this->end->getTimestamp() - $this->start->getTimestamp();

            return intval(floor($length / 86400)) + 1;
        }

        return null;
    }

    /**
     * The day number of the first Day in period.
     */
    public function getFirstDayNumber(): int {
        $earlierPeriods = $this->camp->periods->filter(function (Period $period) {
            if ($period->start < $this->start) {
                return true;
            }

            if ($period->start > $this->start) {
                return false;
            }

            return $period->getId() < $this->getId();
        });

        $firstDayNumber = 1;
        foreach ($earlierPeriods as $period) {
            $firstDayNumber += $period->days->count();
        }

        return $firstDayNumber;
    }

    /**
     * returns the end time of the last day of the period.
     */
    public function getEndOfLastDay(): ?\DateTime {
        if (null === $this->end) {
            return null;
        }

        $endTime = \DateTime::createFromInterface($this->end);
        $endTime->modify('+1 day');

        return $endTime;
    }
}
