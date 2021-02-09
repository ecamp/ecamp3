<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ScheduleEntry extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\ManyToOne(targetEntity="Period")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private ?Period $period = null;

    /**
     * @ORM\ManyToOne(targetEntity="Activity")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private ?Activity $activity = null;

    /**
     * @var int minutes since period start
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $periodOffset = 0;

    /**
     * @var int minutes
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $length = 0;

    /**
     * @ORM\Column(name="`left`", type="float", nullable=true)
     * --> left is a MariaDB keyword, therefore escaping for column name necessary
     */
    private float $left = 0;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private float $width = 1;

    public function getPeriod(): ?Period {
        return $this->period;
    }

    /**
     * @internal Do not set the {@link Period} directly on the ScheduleEntry. Instead use {@see Period::addScheduleEntry()}
     */
    public function setPeriod(?Period $period) {
        $this->period = $period;
    }

    public function getCamp(): ?Camp {
        return (null !== $this->period) ? $this->period->getCamp() : null;
    }

    public function getActivity(): ?Activity {
        return $this->activity;
    }

    /**
     * @internal Do not set the {@see Activity} directly on the ScheduleEntry. Instead use {@see Activity::addScheduleEntry()}
     */
    public function setActivity(?Activity $activity) {
        $this->activity = $activity;
    }

    public function getCategory(): ?Category {
        return (null !== $this->activity) ? $this->activity->getCategory() : null;
    }

    public function getNumberingStyle(): ?string {
        $category = $this->getCategory();

        return (null !== $category) ? $category->getNumberingStyle() : null;
    }

    public function getColor(): ?string {
        $category = $this->getCategory();

        return (null !== $category) ? $category->getColor() : null;
    }

    public function getPeriodOffset(): int {
        return $this->periodOffset;
    }

    /**
     * Minutes since period start.
     *
     * @param int $periodOffset Minutes
     */
    public function setPeriodOffset(int $periodOffset): void {
        $this->periodOffset = $periodOffset;
    }

    public function getLength(): int {
        return $this->length;
    }

    /**
     * Length in minutes.
     *
     * @param int $length Minutes
     */
    public function setLength(int $length): void {
        $this->length = $length;
    }

    public function getLeft(): float {
        return $this->left;
    }

    public function setLeft(float $left): void {
        $this->left = $left;
    }

    public function getWidth(): float {
        return $this->width;
    }

    public function setWidth(float $width): void {
        $this->width = $width;
    }

    public function getDuration(): \DateInterval {
        return new \DateInterval('PT'.$this->length.'M');
    }

    public function getDayNumber(): int {
        return 1 + floor($this->periodOffset / (24 * 60));
    }

    public function getScheduleEntryNumber(): int {
        $dayOffset = floor($this->periodOffset / (24 * 60)) * 24 * 60;

        $expr = Criteria::expr();
        $crit = Criteria::create();
        $crit->where($expr->andX(
            $expr->gte('periodOffset', $dayOffset),
            $expr->lte('periodOffset', $this->periodOffset)
        ));

        /** @var Selectable $scheduleEntriesCollection */
        $scheduleEntriesCollection = $this->period->getScheduleEntries();
        $scheduleEntries = $scheduleEntriesCollection->matching($crit);
        $activityNumber = $scheduleEntries->filter(function (ScheduleEntry $scheduleEntry) {
            if ($scheduleEntry->getNumberingStyle() === $this->getNumberingStyle()) {
                if ($scheduleEntry->periodOffset < $this->periodOffset) {
                    return true;
                }

                // left ScheduleEntry gets lower number
                $seLeft = $scheduleEntry->getLeft();
                $thisLeft = $this->getLeft();

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

    public function getNumber(): string {
        $dayNumber = $this->getDayNumber();
        $scheduleEntryNumber = $this->getScheduleEntryNumber();

        $category = $this->getCategory();
        $scheduleEntryStyledNumber = (null !== $category) ? $category->getStyledNumber($scheduleEntryNumber) : $scheduleEntryNumber;

        return $dayNumber.'.'.$scheduleEntryStyledNumber;
    }
}
