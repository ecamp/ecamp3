<?php

namespace eCamp\Core\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use eCamp\Lib\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="event_instances")
 * @ORM\HasLifecycleCallbacks
 */
class EventInstance extends BaseEntity {
    /**
     * @var Period
     * @ORM\ManyToOne(targetEntity="Period")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $period;

    /**
     * @var Event
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $event;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $start;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $length;

    /**
     * @ORM\Column(name="`left`", type="float", nullable=true)
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

    public function getCamp(): Camp {
        return (null !== $this->period) ? $this->period->getCamp() : null;
    }

    /**
     * @return Event
     */
    public function getEvent() {
        return $this->event;
    }

    public function setEvent($event) {
        $this->event = $event;
    }

    public function getEventCategory(): EventCategory {
        return (null !== $this->event) ? $this->event->getEventCategory() : null;
    }

    public function getNumberingStyle(): string {
        $eventCategory = $this->getEventCategory();

        return (null !== $eventCategory) ? $eventCategory->getNumberingStyle() : null;
    }

    public function getColor(): string {
        $eventCategory = $this->getEventCategory();

        return (null !== $eventCategory) ? $eventCategory->getColor() : null;
    }

    public function getStart(): int {
        return $this->start;
    }

    public function setStart(int $start): void {
        $this->start = $start;
    }

    public function getLength(): int {
        return $this->length;
    }

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

    public function getEventInstanceNumber(): int {
        $dayOffset = floor($this->start / (24 * 60)) * 24 * 60;

        $expr = Criteria::expr();
        $crit = Criteria::create();
        $crit->where($expr->andX(
            $expr->gte('start', $dayOffset),
            $expr->lte('start', $this->start)
        ));

        $eventInstances = $this->period->getEventInstances()->matching($crit);
        $eventNumber = $eventInstances->filter(function (EventInstance $ei) {
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

        return $eventNumber + 1;
    }

    public function getNumber(): string {
        $dayNumber = $this->getDayNumber();
        $eventInstanceNumber = $this->getEventInstanceNumber();
        $eventInstanceStyledNumber = $eventInstanceNumber;

        $category = $this->getEventCategory();
        if (null != $category) {
            $eventInstanceStyledNumber = $category->getStyledNumber($eventInstanceNumber);
        }

        return $dayNumber.'.'.$eventInstanceStyledNumber;
    }
}
