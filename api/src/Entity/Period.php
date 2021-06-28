<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PeriodRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PeriodRepository::class)
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

    public function addDay(Day $day): void {
        $day->setPeriod($this);
        $this->days->add($day);
    }

    public function removeDay(Day $day): void {
        $day->period = null;
        $this->days->removeElement($day);
    }

    public function getScheduleEntries(): array {
        return $this->scheduleEntries->getValues();
    }

    public function addScheduleEntry(ScheduleEntry $scheduleEntry): void {
        $scheduleEntry->period = $this;
        $this->scheduleEntries->add($scheduleEntry);
    }

    public function removeScheduleEntry(ScheduleEntry $scheduleEntry): void {
        $scheduleEntry->period = null;
        $this->scheduleEntries->removeElement($scheduleEntry);
    }

    public function getMaterialItems(): array {
        return $this->materialItems->getValues();
    }

    public function addMaterialItem(MaterialItem $materialItem): void {
        $materialItem->period = $this;
        $this->materialItems->add($materialItem);
    }

    public function removeMaterialItem(MaterialItem $materialItem): void {
        $materialItem->period = null;
        $this->materialItems->removeElement($materialItem);
    }
}
