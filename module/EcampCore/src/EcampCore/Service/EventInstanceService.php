<?php

namespace EcampCore\Service;

use EcampCore\Acl\Privilege;
use EcampCore\Entity\Day;
use EcampCore\Entity\Period;
use EcampCore\Entity\Event;
use EcampCore\Entity\EventInstance;

use EcampCore\Repository\EventInstanceRepository;
use EcampCore\Repository\PeriodRepository;
use EcampLib\Service\ServiceBase;
use EcampCore\Repository\DayRepository;
use EcampLib\Validation\ValidationException;

class EventInstanceService
    extends ServiceBase
{

    private $periodRepository;
    private $dayRepository;
    private $eventInstanceRepository;

    public function __construct(
        PeriodRepository $periodRepository,
        DayRepository $dayRepository,
        EventInstanceRepository $eventInstanceRepository
    ){
        $this->periodRepository = $periodRepository;
        $this->dayRepository = $dayRepository;
        $this->eventInstanceRepository = $eventInstanceRepository;
    }

    /**
     * @param  string        $id
     * @return EventInstance
     */
    public function Get($id)
    {
        $eventInstance = null;

        if (is_string($id)) {
            $eventInstance = $this->eventInstanceRepository->find($id);
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
        $eventInstance = new EventInstance($event);

        $this->Update($eventInstance, $data);
        $this->persist($eventInstance);

        return $eventInstance;
    }

    public function Update($eventInstance, $data)
    {
        $period = null;
        $eventInstance = $this->Get($eventInstance);

        if (isset($data['period'])) {
            $period = $this->periodRepository->find($data['period']);
        }

        if (!isset($data['minOffsetStart']) && isset($data['startday']) && isset($data['starttime'])) {
            /* @var $startDay \EcampCore\Entity\Day */
            $startDay = $this->dayRepository->find($data['startday']);
            $period = $startDay->getPeriod();

            $eventInstanceStart = new \DateTime($startDay->getStart()->format('Y-m-d ') . $data['starttime']);
            $data['minOffsetStart'] =
                round(($eventInstanceStart->getTimestamp() - $period->getStart()->getTimestamp()) / 60);
        }

        if (!isset($data['minOffsetEnd']) && isset($data['endday']) && isset($data['endtime'])) {
            /* @var $endDay \EcampCore\Entity\Day */
            $endDay = $this->dayRepository->find($data['endday']);
            $period = $endDay->getPeriod();

            $eventInstanceEnd = new \DateTime($endDay->getStart()->format('Y-m-d ') . $data['endtime']);
            $data['minOffsetEnd'] =
                round(($eventInstanceEnd->getTimestamp() - $period->getStart()->getTimestamp()) / 60);
        }

        if ($period != null) {
            $eventInstance->setPeriod($period);
        }

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

    public function Delete($eventInstance)
    {
        $eventInstance = $this->Get($eventInstance);

        if ($eventInstance != null) {
            $event = $eventInstance->getEvent();

            $this->remove($eventInstance);

            if ($event->getEventInstances()->count() == 0) {
                $this->remove($event);
            }

            return true;
        }

        return false;
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
