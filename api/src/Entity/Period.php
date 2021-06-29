<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
#[ApiResource]
class Period extends BaseEntity {
    /**
     * @ORM\OneToMany(targetEntity="Day", mappedBy="period", orphanRemoval=true)
     * @ORM\OrderBy({"dayOffset": "ASC"})
     *
     * @var Day[]
     */
    public $days;

    /**
     * @ORM\OneToMany(targetEntity="ScheduleEntry", mappedBy="period")
     * @ORM\OrderBy({"periodOffset": "ASC"})
     *
     * @var ScheduleEntry[]
     */
    public $scheduleEntries;

    /**
     * @ORM\OneToMany(targetEntity="MaterialItem", mappedBy="period")
     *
     * @var MaterialItem[]
     */
    public $materialItems;

    /**
     * @ORM\ManyToOne(targetEntity="Camp", inversedBy="periods")
     * @ORM\JoinColumn(nullable=false)
     */
    public ?Camp $camp = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public ?string $description = null;

    /**
     * @ORM\Column(type="date")
     */
    public ?DateTimeInterface $start = null;

    /**
     * @ORM\Column(name="`end`", type="date")
     */
    public ?DateTimeInterface $end = null;

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
