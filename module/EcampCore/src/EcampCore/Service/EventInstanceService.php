<?php

namespace EcampCore\Service;

use EcampCore\Entity\Day;
use EcampCore\Entity\Period;
use EcampCore\Entity\Event;
use EcampCore\Entity\EventInstance;

use EcampLib\Service\ServiceBase;
use EcampCore\Repository\DayRepository;

/**
 * @method EcampCore\Service\EventInstanceService Simulate
 */
class EventInstanceService
    extends ServiceBase
{

    private $dayRepo;

    public function __construct(
        DayRepository $dayRepo
    ){
        $this->dayRepo = $dayRepo;
    }

    /**
     * @param  string        $id
     * @return EventInstance
     */
    public function Get($id)
    {
        $eventInstance = $this->repo()->eventInstanceRepository()->find($id);
        $this->validationContextAssert($eventInstance);

        return $eventInstance;
    }

    /**
     * @param  Day                                         $day
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function GetByDay(Day $day)
    {
        $this->validationContextAssert($day);

        return $this->repo()->eventInstanceRepository()->findByDay($day);
    }

    /**
     * @param  Period                                      $period
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function GetByPeriod(Period $period)
    {
        $this->validationContextAssert($period);

        return $this->repo()->eventInstanceRepository()->findByPeriod($period);
    }

    /**
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function GetByCamp()
    {
        $camp = $this->getContextProvider()->getCamp();

        return $this->repo()->eventInstanceRepository()->findByCamp($camp);
    }

    /**
     * @param  Event         $event
     * @return EventInstance
     */
    public function Create(Event $event, $data)
    {
        $startDayId = $data['eventInstance']['startday'];
        $endDayId = $data['eventInstance']['endday'];
        $startTime = $data['eventInstance']['starttime'];
        $endTime = $data['eventInstance']['endtime'];

        /* @var $startDay \EcampCore\Entity\Day */
        $startDay = $this->dayRepo->find($startDayId);
        $endDay = $this->dayRepo->find($endDayId);
        $period = $startDay->getPeriod();

        $eventInstanceStart = new \DateTime($startDay->getStart()->format('Y-m-d ') . $startTime);
        $eventInstanceEnd = new \DateTime($endDay->getStart()->format('Y-m-d ') . $endTime);

        $offset = date_diff($period->getStart(), $eventInstanceStart);
        $duration = date_diff($eventInstanceStart, $eventInstanceEnd);

        $eventInstance = new EventInstance($event);
        $eventInstance->setPeriod($period);

        $eventInstance->setOffset($offset);
        $eventInstance->setDuration($duration);

        $this->persist($eventInstance);

        return $eventInstance;
    }

    /**
     * @param EventInstance             $eventInstance
     * @param Period                    $period
     * @param DateTime|DateInterval|int $start
     */
    public function Move(EventInstance $eventInstance, Period $period , $start)
    {
        $this->validationContextAssert($period);
        $this->validationContextAssert($eventInstance);

        if ($start instanceof \DateTime) {
            $start = $period->getStart()->diff($start);
        }

        $eventInstance->setPeriod($period);
        $eventInstance->setOffset($start);
    }

    /**
     * @param EventInstance    $eventInstance
     * @param DateInterval|int $duration
     */
    public function Resize(EventInstance $eventInstance, $duration)
    {
        $this->validationContextAssert($eventInstance);
        $eventInstance->setDuration($duration);
    }

    public function Delete(EventInstance $eventInstance)
    {
        $this->validationContextAssert($eventInstance);

        $event = $eventInstance->getEvent();
        $event->getEventInstances()->removeElement($eventInstance);

        if ($event->getEventInstances()->count() == 0) {
            // TODO: What to do with Event without any EventInstnace any more?
        }

        $this->remove($eventInstance);
    }

}
