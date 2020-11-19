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
     * @var Period
     * @ORM\ManyToOne(targetEntity="Period")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $period;

    /**
     * @var Activity
     * @ORM\ManyToOne(targetEntity="Activity")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $activity;

    /**
     * @var int minutes since period start
     * @ORM\Column(type="integer", nullable=false)
     */
    private $periodOffset;

    /**
     * @var int minutes
     * @ORM\Column(type="integer", nullable=false)
     */
    private $length;

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

    /**
     * @internal Do not set the {@link Period} directly on the ScheduleEntry. Instead use {@see Period::addScheduleEntry()}
     *
     * @param $period
     */
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

    /**
     * @internal Do not set the {@see Activity} directly on the ScheduleEntry. Instead use {@see Activity::addScheduleEntry()}
     *
     * @param $activity
     */
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

        $scheduleEntries = $this->period->getScheduleEntries()->matching($crit);
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

        $category = $this->getActivityCategory();
        $scheduleEntryStyledNumber = (null !== $category) ? $category->getStyledNumber($scheduleEntryNumber) : $scheduleEntryNumber;

        return $dayNumber.'.'.$scheduleEntryStyledNumber;
    }
}
