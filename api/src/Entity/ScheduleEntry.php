<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
#[ApiResource]
class ScheduleEntry extends BaseEntity implements BelongsToCampInterface {
    /**
     * @ORM\ManyToOne(targetEntity="Period", inversedBy="scheduleEntries")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    public ?Period $period = null;

    /**
     * @internal Do not set the {@see Activity} directly on the ScheduleEntry. Instead use {@see Activity::addScheduleEntry()}
     * @ORM\ManyToOne(targetEntity="Activity", inversedBy="scheduleEntries")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    public ?Activity $activity = null;

    /**
     * @var int minutes since period start
     * @ORM\Column(type="integer", nullable=false)
     */
    public int $periodOffset = 0;

    /**
     * @var int minutes
     * @ORM\Column(type="integer", nullable=false)
     */
    public int $length = 0;

    /**
     * @ORM\Column(name="`left`", type="float", nullable=true)
     * --> left is a MariaDB keyword, therefore escaping for column name necessary
     */
    public float $left = 0;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    public float $width = 1;

    #[ApiProperty(readable: false)]
    public function getCamp(): ?Camp {
        return $this->period?->camp;
    }

    public function getDay(): Day {
        $dayNumber = $this->getDayNumber();

        return $this->period->days->filter(function (Day $day) use ($dayNumber) {
            return $day->getDayNumber() === $dayNumber;
        })->first();
    }

    public function getNumberingStyle(): ?string {
        return $this->activity?->category?->numberingStyle;
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
        $scheduleEntriesCollection = $this->period->scheduleEntries;
        $scheduleEntries = $scheduleEntriesCollection->matching($crit);
        $activityNumber = $scheduleEntries->filter(function (ScheduleEntry $scheduleEntry) {
            if ($scheduleEntry->getNumberingStyle() === $this->getNumberingStyle()) {
                if ($scheduleEntry->periodOffset < $this->periodOffset) {
                    return true;
                }

                // left ScheduleEntry gets lower number
                $seLeft = $scheduleEntry->left;
                $thisLeft = $this->left;

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

        $scheduleEntryStyledNumber = $this->activity?->category?->getStyledNumber($scheduleEntryNumber) ?? $scheduleEntryNumber;

        return $dayNumber.'.'.$scheduleEntryStyledNumber;
    }
}
