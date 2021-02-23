<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Types\DateUtc;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Period extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\OneToMany(targetEntity="Day", mappedBy="period", orphanRemoval=true)
     * @ORM\OrderBy({"dayOffset": "ASC"})
     */
    protected Collection $days;

    /**
     * @ORM\OneToMany(targetEntity="ScheduleEntry", mappedBy="period")
     */
    protected Collection $scheduleEntries;

    /**
     * @ORM\OneToMany(targetEntity="MaterialItem", mappedBy="period")
     */
    protected Collection $materialItems;

    /**
     * @ORM\ManyToOne(targetEntity="Camp")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private ?Camp $camp = null;

    /**
     * @ORM\Column(type="date", nullable=false)
     */
    private ?DateUtc $start = null;

    /**
     * @ORM\Column(type="date", nullable=false)
     */
    private ?DateUtc $end = null;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private ?string $description = null;

    public function __construct() {
        parent::__construct();

        $this->days = new ArrayCollection();
        $this->scheduleEntries = new ArrayCollection();
        $this->materialItems = new ArrayCollection();
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    public function setCamp(?Camp $camp): void {
        $this->camp = $camp;
    }

    public function getStart(): ?DateUtc {
        return (null !== $this->start) ? (clone $this->start) : null;
    }

    public function setStart(?DateUtc $start): void {
        $this->start = clone $start;

        if (null != $this->end && $this->end < $start) {
            $this->setEnd($start);
        }
    }

    public function getEnd(): ?DateUtc {
        return (null !== $this->end) ? (clone $this->end) : null;
    }

    public function setEnd(?DateUtc $end): void {
        $this->end = clone $end;

        if (null != $this->start && $this->start > $end) {
            $this->setStart($end);
        }
    }

    public function getDurationInDays(): int {
        $start = $this->getStart();
        $end = $this->getEnd();

        if (null !== $start && null !== $end) {
            return $start->diff($end)->days + 1;
        }

        return 0;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function getDays(): Collection {
        return $this->days;
    }

    public function addDay(Day $day): void {
        $day->setPeriod($this);
        $this->days->add($day);
    }

    public function removeDay(Day $day): void {
        $day->setPeriod(null);
        $this->days->removeElement($day);
    }

    public function getScheduleEntries(): Collection {
        return $this->scheduleEntries;
    }

    public function addScheduleEntry(ScheduleEntry $scheduleEntry): void {
        $scheduleEntry->setPeriod($this);
        $this->scheduleEntries->add($scheduleEntry);
    }

    public function removeScheduleEntry(ScheduleEntry $scheduleEntry): void {
        $scheduleEntry->setPeriod(null);
        $this->scheduleEntries->removeElement($scheduleEntry);
    }

    public function getMaterialItems(): Collection {
        return $this->materialItems;
    }

    public function addMaterialItem(MaterialItem $materialItem): void {
        $materialItem->setPeriod($this);
        $this->materialItems->add($materialItem);
    }

    public function removeMaterialItem(MaterialItem $materialItem): void {
        $materialItem->setPeriod(null);
        $this->materialItems->removeElement($materialItem);
    }
}
