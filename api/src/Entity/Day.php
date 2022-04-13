<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\DayRepository;
use App\Serializer\Normalizer\RelatedCollectionLink;
use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * A day in a time period of a camp. This is represented as a reference to the time period
 * along with a number of days offset from the period's starting date. This is to make it
 * easier to move the whole periods to different dates. Days are created automatically when
 * creating or updating periods, and are not writable through the API directly.
 */
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => self::COLLECTION_NORMALIZATION_CONTEXT,
            'security' => 'is_authenticated()',
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => self::ITEM_NORMALIZATION_CONTEXT,
            'security' => 'is_granted("CAMP_COLLABORATOR", object) or is_granted("CAMP_IS_PROTOTYPE", object)',
        ],
    ],
    denormalizationContext: ['groups' => ['write']],
    normalizationContext: ['groups' => ['read']],
    order: ['period.start', 'dayOffset']
)]
#[ApiFilter(SearchFilter::class, properties: ['period'])]
#[UniqueEntity(fields: ['period', 'dayOffset'])]
#[ORM\Entity(repositoryClass: DayRepository::class)]
#[ORM\UniqueConstraint(name: 'offset_period_idx', columns: ['periodId', 'dayOffset'])]
class Day extends BaseEntity implements BelongsToCampInterface {
    public const ITEM_NORMALIZATION_CONTEXT = [
        'groups' => [
            'read',
            'Day:DayResponsibles',
        ],
        'swagger_definition_name' => 'read',
    ];

    public const COLLECTION_NORMALIZATION_CONTEXT = [
        'groups' => [
            'read',
            'Day:DayResponsibles',
        ],
    ];

    /**
     * The list of people who have a whole-day responsibility on this day.
     */
    #[ApiProperty(writable: false, example: '["/day_responsibles/1a2b3c4d"]')]
    #[Groups(['read'])]
    #[ORM\OneToMany(targetEntity: 'DayResponsible', mappedBy: 'day', orphanRemoval: true)]
    public Collection $dayResponsibles;

    /**
     * The time period that this day belongs to.
     */
    #[ApiProperty(example: '/periods/1a2b3c4d')]
    #[Groups(['read'])]
    #[ORM\ManyToOne(targetEntity: 'Period', inversedBy: 'days')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    public ?Period $period = null;

    /**
     * The 0-based offset in days from the period's start date when this day starts.
     */
    #[ApiProperty(writable: false, example: '1')]
    #[Groups(['read'])]
    #[ORM\Column(type: 'integer')]
    public int $dayOffset = 0;

    public function __construct() {
        parent::__construct();
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
    #[Groups(['read'])]
    public function getDayNumber(): int {
        return $this->period->getFirstDayNumber() + $this->dayOffset;
    }

    /**
     * All scheduleEntries in this day's period which overlap with this day (using midnight as cutoff).
     *
     * @return ScheduleEntry[]
     */
    #[ApiProperty(example: '/schedule_entries?period=%2Fperiods%2F1a2b3c4d&start%5Bstrictly_before%5D=2022-01-03T00%3A00%3A00%2B00%3A00&end%5Bafter%5D=2022-01-02T00%3A00%3A00%2B00%3A00')]
    #[RelatedCollectionLink(ScheduleEntry::class, ['period' => 'period', 'start[strictly_before]' => 'end', 'end[after]' => 'start'])]
    #[Groups(['read'])]
    public function getScheduleEntries(): array {
        return array_filter(
            $this->period->getScheduleEntries(),
            // filters all scheduleEntries which overlap with the current day
            function (ScheduleEntry $scheduleEntry) {
                return $scheduleEntry->getStart() >= $this->getEnd()     // starts at or after midnight of current day
                        && $scheduleEntry->getEnd() < $this->getStart(); // ends strictly before midnight of next day
            }
        );
    }

    /**
     * The start date and time of the day. This is a read-only convenience property.
     *
     * @return null|DateTime
     */
    #[ApiProperty(example: '2022-01-02T00:00:00+00:00', openapiContext: ['format' => 'date'])]
    #[Groups(['read'])]
    public function getStart(): ?DateTime {
        try {
            $start = $this->period?->start ? DateTime::createFromInterface($this->period->start) : null;
            $start?->add(new DateInterval('P'.$this->dayOffset.'D'));

            return $start;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * The end date and time of the day. This is a read-only convenience property.
     *
     * @return null|DateTime
     */
    #[ApiProperty(example: '2022-01-03T00:00:00+00:00', openapiContext: ['format' => 'date'])]
    #[Groups(['read'])]
    public function getEnd(): ?DateTime {
        try {
            $end = $this->period?->start ? DateTime::createFromInterface($this->period->start) : null;
            $end?->add(new DateInterval('P'.($this->dayOffset + 1).'D'));

            return $end;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * DayResponsible.
     */

    /**
     * @return DayResponsible[]
     */
    #[ApiProperty(readableLink: true)]
    #[SerializedName('dayResponsibles')]
    #[Groups(['Day:DayResponsibles'])]
    public function getEmbeddedDayResponsibles(): array {
        return $this->getDayResponsibles();
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
