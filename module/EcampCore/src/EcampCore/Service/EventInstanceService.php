<?php

namespace EcampCore\Service;

use EcampCore\Acl\Privilege;
use EcampCore\Entity\Day;
use EcampCore\Entity\Period;
use EcampCore\Entity\Event;
use EcampCore\Entity\EventInstance;

use EcampCore\Repository\EventInstanceRepository;
use EcampLib\Service\ServiceBase;
use EcampCore\Repository\DayRepository;
use EcampLib\Validation\ValidationException;

class EventInstanceService
    extends ServiceBase
{

    private $eventInstanceRepo;
    private $dayRepo;

    public function __construct(
        EventInstanceRepository $eventInstanceRepo,
        DayRepository $dayRepo
    ){
        $this->eventInstanceRepo = $eventInstanceRepo;
        $this->dayRepo = $dayRepo;
    }

    /**
     * @param  string        $id
     * @return EventInstance
     */
    public function Get($id)
    {
        $eventInstance = null;

        if (is_string($id)) {
            $eventInstance = $this->eventInstanceRepo->find($id);
        }

        if ($id instanceof EventInstance) {
            $eventInstance = $id;
        }

        return $eventInstance;

        if ($eventInstance != null) {
            return $this->aclIsAllowed($eventInstance->getCamp(), Privilege::CAMP_CONTRIBUTE) ? $eventInstance : null;
        }

        return null;
    }

    /**
     * @param  Day                                          $day
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    /*
    public function GetByDay(Day $day)
    {
        $this->validationContextAssert($day);

        return $this->repo()->eventInstanceRepository()->findByDay($day);
    }
    */

    /**
     * @param  Period                                       $period
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    /*
    public function GetByPeriod(Period $period)
    {
        $this->validationContextAssert($period);

        return $this->repo()->eventInstanceRepository()->findByPeriod($period);
    }
    */

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    /*
    public function GetByCamp()
    {
        $camp = $this->getContextProvider()->getCamp();

        return $this->repo()->eventInstanceRepository()->findByCamp($camp);
    }
    */

    /**
     * @param Event $event
     * @param $data
     * @throws \Exception
     * @return EventInstance
     */
    public function Create(Event $event, $data)
    {
        $startDayId = $data['startday'];
        $endDayId = $data['endday'];
        $startTime = $data['starttime'];
        $endTime = $data['endtime'];

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

    public function Update($eventInstanceId, $data)
    {
        $eventInstance = $this->Get($eventInstanceId);
        //$this->aclRequire($eventInstance->getCamp(), Privilege::CAMP_CONTRIBUTE);

        $updateKeys = array_intersect(
            array_keys($data),
            array('minOffsetStart', 'minOffsetEnd', 'leftOffset', 'width')
        );

        $eventInstanceValidationForm =
            $this->createValidationForm($eventInstance, $data, $updateKeys);

        if (!$eventInstanceValidationForm->isValid()) {
            throw ValidationException::FromForm($eventInstanceValidationForm);
        }

        return $eventInstance;
    }

    /**
     * @param EventInstance               $eventInstance
     * @param Period                      $period
     * @param \DateTime|\DateInterval|int $start
     */
    /*
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
    */

    /**
     * @param EventInstance     $eventInstance
     * @param \DateInterval|int $duration
     */
    /*
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
    */

}
