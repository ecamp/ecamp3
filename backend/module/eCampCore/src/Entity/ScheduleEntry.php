<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\Criteria;
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
    private Period $period;

    /**
     * @ORM\ManyToOne(targetEntity="Activity")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private Activity $activity;

    /**
     * Minutes since period start.
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $start;

    /**
     * Length in Minutes.
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $length;

    /**
     * @ORM\Column(name="`left`", type="float", nullable=true)
     * --> left is a MariaDB keyword, therefore escaping for column name necessary
     */
    private $left;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $width;

    public function __construct() {
        parent::__construct();
    }

    /**
     * @return Period
     */
    public function getPeriod() {
        return $this->period;
    }

    public function setPeriod($period) {
        $this->period = $period;
    }

    public function getCamp() {
        return (null !== $this->period) ? $this->period->getCamp() : null;
    }

    /**
     * @return Activity
     */
    public function getActivity() {
        return $this->activity;
    }

    public function setActivity($activity) {
        $this->activity = $activity;
    }

    public function getActivityCategory(): ?ActivityCategory {
        return (null !== $this->activity) ? $this->activity->getActivityCategory() : null;
    }

    public function getNumberingStyle(): ?string {
        $activityCategory = $this->getActivityCategory();

        return (null !== $activityCategory) ? $activityCategory->getNumberingStyle() : null;
    }

    public function getColor(): ?string {
        $activityCategory = $this->getActivityCategory();

        return (null !== $activityCategory) ? $activityCategory->getColor() : null;
    }

    public function getStart(): int {
        return $this->start;
    }

    /**
     * Minutes since period start.
     *
     * @param int $start Minutes
     */
    public function setStart(int $start): void {
        $this->start = $start;
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

    /**
     * @return mixed
     */
    public function getLeft() {
        return $this->left ?: 0;
    }

    public function setLeft($left): void {
        $this->left = $left;
    }

    /**
     * @return mixed
     */
    public function getWidth() {
        return $this->width ?: 1;
    }

    public function setWidth($width): void {
        $this->width = $width;
    }

    public function getDuration(): \DateInterval {
        return new \DateInterval('PT'.$this->length.'M');
    }

    public function getStartTime(): \DateTime {
        $start = $this->getPeriod()->getStart();
        $start->add(new \DateInterval('PT'.$this->start.'M'));

        return $start;
    }

    public function getEndTime(): \DateTime {
        $end = $this->getStartTime();
        $end->add($this->getDuration());

        return $end;
    }

    public function getDayNumber(): int {
        return 1 + floor($this->start / (24 * 60));
    }

    public function getScheduleEntryNumber(): int {
        $dayOffset = floor($this->start / (24 * 60)) * 24 * 60;

        $expr = Criteria::expr();
        $crit = Criteria::create();
        $crit->where($expr->andX(
            $expr->gte('start', $dayOffset),
            $expr->lte('start', $this->start)
        ));

        $scheduleEntries = $this->period->getScheduleEntries()->matching($crit);
        $activityNumber = $scheduleEntries->filter(function (ScheduleEntry $ei) {
            if ($ei->getNumberingStyle() === $this->getNumberingStyle()) {
                if ($ei->start < $this->start) {
                    return true;
                }

                $eiLeft = $ei->left ?: 0;
                $thisLeft = $this->left ?: 0;

                if ($eiLeft < $thisLeft) {
                    return true;
                }
                if ($eiLeft === $thisLeft) {
                    if ($ei->createTime < $this->createTime) {
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
        $scheduleEntryStyledNumber = $scheduleEntryNumber;

        $category = $this->getActivityCategory();
        if (null != $category) {
            $scheduleEntryStyledNumber = $category->getStyledNumber($scheduleEntryNumber);
        }

        return $dayNumber.'.'.$scheduleEntryStyledNumber;
    }
}
