<?php

namespace EcampCore\Service;

use EcampCore\Service\Params\Params;

use EcampCore\Entity\Period;
use EcampCore\Entity\Day;
use EcampLib\Service\ServiceBase;
use EcampCore\Acl\Privilege;

/**
 * @method EcampCore\Service\DayService Simulate
 */
class DayService
    extends ServiceBase
{

    /**
     * @param  Period $period
     * @return Day
     */
    public function AppendDay(Period $period)
    {
        $this->aclRequire($period->getCamp(), Privilege::CAMP_CONFIGURE);

        $day = new Day($period, $period->getNumberOfDays());
        $period->getDays()->add($day);

        $this->persist($day);

        return $day;
    }

    /**
     * @param Period $period
     */
    public function RemoveDay(Period $period)
    {
        $this->aclRequire($period->getCamp(), Privilege::CAMP_CONFIGURE);

        /* @var $day \EcampCore\Entity\Day */
        $day = $period->getDays()->last();

        foreach ($period->getEventInstances() as $eventInstance) {
            /* @var $eventInstance \EcampCore\Entity\EventInstance */
            if ($eventInstance->getEndTime() > $day->getStart()) {
                throw new \Exception("Period can not be resized, because a Event takes place at a day which will be removed");
            }
        }

        $period->getDays()->removeElement($day);

        $this->remove($day);
    }

    /**
     * @param Day    $day
     * @param Params $param
     */
    public function Update(Day $day, Params $param)
    {
        $this->aclRequire($day->getCamp(), Privilege::CAMP_CONTRIBUTE);

        if ($param->hasElement('notes')) {
            $day->setNotes($param->getValue('notes'));
        }
    }

}
