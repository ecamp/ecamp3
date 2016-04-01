<?php

namespace EcampCore\Service;

use EcampCore\Entity\Period;
use EcampCore\Entity\Day;
use EcampCore\Acl\Privilege;
use EcampCore\Entity\Story;

class DayService extends Base\ServiceBase
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

    public function UpdateStory(Day $day, $notes)
    {
        $this->aclRequire($day->getCamp(), Privilege::CAMP_CONTRIBUTE);

        $story = $day->getStory();
        if ($story == null) {
            $story = new Story();
            $day->setStory($story);
        }

        $story->setNotes($notes);
    }

}
