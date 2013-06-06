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

        // Can the day be deletet?
        // What about the EventInstances?

        $day = $period->getDays()->last();
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
