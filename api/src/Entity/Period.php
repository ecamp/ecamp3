<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\PeriodRepository;
use App\Serializer\Normalizer\RelatedCollectionLink;
use DateTimeInterface;
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
 *
 * @ORM\Entity(repositoryClass=PeriodRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => 'is_authenticated()'],
        'post' => [
            'denormalization_context' => ['groups' => ['write', 'create']],
            'security_post_denormalize' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)',
        ],
    ],
    itemOperations: [
        'get' => [
            'security' => 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)',
            'normalization_context' => self::ITEM_NORMALIZATION_CONTEXT,
        ],
        'patch' => ['security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'],
        'delete' => ['security' => 'is_granted("CAMP_MEMBER", object) or is_granted("CAMP_MANAGER", object)'],
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
)]
#[ApiFilter(SearchFilter::class, properties: ['camp'])]
class Period extends BaseEntity implements BelongsToCampInterface {
    public const ITEM_NORMALIZATION_CONTEXT = [
        'groups' => ['read', 'Period:Camp', 'Period:Days'],
        'swagger_definition_name' => 'read',
    ];

    /**
     * The days in this time period. These are generated automatically.
     *
     * @ORM\OneToMany(targetEntity="Day", mappedBy="period", orphanRemoval=true, cascade={"persist"})
     * @ORM\OrderBy({"dayOffset": "ASC"})
     */
    #[ApiProperty(writable: false, example: '["/days?period=/periods/1a2b3c4d"]')]
    #[Groups(['read'])]
    public Collection $days;

    /**
     * All time slots for programme that are part of this time period. A schedule entry
     * may span over multiple days, but may not end later than the period.
     *
     * @ORM\OneToMany(targetEntity="ScheduleEntry", mappedBy="period")
     * @ORM\OrderBy({"periodOffset": "ASC"})
     */
    #[ApiProperty(writable: false, example: '["/schedule_entries/1a2b3c4d"]')]
    #[Groups(['read'])]
    public Collection $scheduleEntries;

    /**
     * Material items that are assigned directly to the period, as opposed to individual
     * activities.
     *
     * @ORM\OneToMany(targetEntity="MaterialItem", mappedBy="period")
     */
    public Collection $materialItems;

    /**
     * The camp that this time period belongs to. Cannot be changed once the period is created.
     *
     * @ORM\ManyToOne(targetEntity="Camp", inversedBy="periods")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Assert\Valid(groups: ['Period:delete'])]
    #[ApiProperty(example: '/camps/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    public ?Camp $camp = null;

    /**
     * A free text name for the period. Useful to distinguish multiple periods in the same camp.
     *
     * TODO: Make non-nullable in the DB
     *
     * @ORM\Column(type="text", nullable=true)
     */
    #[Assert\NotBlank]
    #[ApiProperty(example: 'Hauptlager')]
    #[Groups(['read', 'write'])]
    public ?string $description = null;

    /**
     * The day on which the period starts, as an ISO date string. Should not be after "end".
     *
     * TODO: Do we keep on implementing the deletion of activities when shortening the period,
     *       or do we just validate that there must be no activities that would be lost?
     *       When implementing dangerous operations on the backend, there is no way to enforce
     *       a user confirmation dialog. But then again, we also support deleting whole camps
     *       that aren't empty...
     *
     * @ORM\Column(type="date")
     */
    #[Assert\LessThanOrEqual(propertyPath: 'end')]
    #[ApiProperty(example: '2022-01-01', openapiContext: ['format' => 'date'])]
    #[Context(
        normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'],
        denormalizationContext: [DateTimeNormalizer::FORMAT_KEY => '!Y-m-d']
    )]
    #[Groups(['read', 'write'])]
    public ?DateTimeInterface $start = null;

    /**
     * The (inclusive) day at the end of which the period ends, as an ISO date string. Should
     * not be before "start".
     *
     * @ORM\Column(name="`end`", type="date")
     */
    #[Assert\GreaterThanOrEqual(propertyPath: 'start')]
    #[ApiProperty(example: '2022-01-08', openapiContext: ['format' => 'date'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => '!Y-m-d'])]
    #[Context(
        normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'],
        denormalizationContext: [DateTimeNormalizer::FORMAT_KEY => '!Y-m-d']
    )]
    #[Groups(['read', 'write'])]
    public ?DateTimeInterface $end = null;

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
}
