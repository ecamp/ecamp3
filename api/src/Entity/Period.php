<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\PeriodRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A time period in which the programme of a camp will take place. There may be multiple
 * periods in a camp, but they may not overlap. A period is made up of one or more full days.
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['security' => 'is_fully_authenticated()'],
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
#[Entity(repositoryClass: PeriodRepository::class)]
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
    #[OneToMany(targetEntity: 'Day', mappedBy: 'period', orphanRemoval: true)]
    #[OrderBy(value: ['dayOffset' => 'ASC'])]
    public Collection $days;

    /**
     * All time slots for programme that are part of this time period. A schedule entry
     * may span over multiple days, but may not end later than the period.
     */
    #[ApiProperty(writable: false, example: '["/schedule_entries/1a2b3c4d"]')]
    #[Groups(['read'])]
    #[OneToMany(targetEntity: 'ScheduleEntry', mappedBy: 'period')]
    #[OrderBy(value: ['periodOffset' => 'ASC'])]
    public Collection $scheduleEntries;

    /**
     * Material items that are assigned directly to the period, as opposed to individual
     * activities.
     */
    #[ApiProperty(writable: false, example: '["/material_items/1a2b3c4d"]')]
    #[Groups(['read'])]
    #[OneToMany(targetEntity: 'MaterialItem', mappedBy: 'period')]
    public Collection $materialItems;

    /**
     * The camp that this time period belongs to. Cannot be changed once the period is created.
     */
    #[ApiProperty(example: '/camps/1a2b3c4d')]
    #[Groups(['read', 'create'])]
    #[ManyToOne(targetEntity: 'Camp', inversedBy: 'periods')]
    #[JoinColumn(nullable: false)]
    public ?Camp $camp = null;

    /**
     * A free text name for the period. Useful to distinguish multiple periods in the same camp.
     *
     * TODO: Make non-nullable in the DB
     */
    #[Assert\NotBlank]
    #[ApiProperty(example: 'Hauptlager')]
    #[Groups(['read', 'write'])]
    #[Column(type: 'text', nullable: true)]
    public ?string $description = null;

    /**
     * The day on which the period starts, as an ISO date string. Should not be after "end".
     *
     * TODO: Do we keep on implementing the deletion of activities when shortening the period,
     *       or do we just validate that there must be no activities that would be lost?
     *       When implementing dangerous operations on the backend, there is no way to enforce
     *       a user confirmation dialog. But then again, we also support deleting whole camps
     *       that aren't empty...
     */
    #[Assert\LessThanOrEqual(propertyPath: 'end')]
    #[ApiProperty(example: '2022-01-01', openapiContext: ['format' => 'date'])]
    #[Groups(['read', 'write'])]
    #[Column(type: 'date')]
    public ?DateTimeInterface $start = null;

    /**
     * The (inclusive) day at the end of which the period ends, as an ISO date string. Should
     * not be before "start".
     */
    #[Assert\GreaterThanOrEqual(propertyPath: 'start')]
    #[ApiProperty(example: '2022-01-08', openapiContext: ['format' => 'date'])]
    #[Groups(['read', 'write'])]
    #[Column(name: '`end`', type: 'date')]
    public ?DateTimeInterface $end = null;

    public function __construct() {
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
