<?php

namespace CoreApi\Service;


use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;

use CoreApi\Entity\Day;
use CoreApi\Entity\Period;
use CoreApi\Entity\Event;
use CoreApi\Entity\EventInstance;

use CoreApi\Service\Params\Params;


/**
 * @method CoreApi\Service\LoginService Simulate
 */
class EventInstanceService 
	extends ServiceBase
{
	
	/**
	 * @var Core\Repository\EventInstanceRepository
	 * @Inject Core\Repository\EventInstanceRepository
	 */
	protected $eventInstanceRepo;
	
	
	/**
	 * Setup ACL
	 * @return void
	 */
	public function _setupAcl()
	{
		$this->acl->allow(DefaultAcl::CAMP_MEMBER, $this, 'Get');
		$this->acl->allow(DefaultAcl::CAMP_MEMBER, $this, 'GetByDay');
		$this->acl->allow(DefaultAcl::CAMP_MEMBER, $this, 'GetByPeriod');
		$this->acl->allow(DefaultAcl::CAMP_MEMBER, $this, 'GetByCamp');
		$this->acl->allow(DefaultAcl::CAMP_MEMBER, $this, 'Create');
		$this->acl->allow(DefaultAcl::CAMP_MEMBER, $this, 'Move');
		$this->acl->allow(DefaultAcl::CAMP_MEMBER, $this, 'Resize');
		$this->acl->allow(DefaultAcl::CAMP_MEMBER, $this, 'Delete');
	}
	
	
	/**
	 * @param string $id
	 * @return EventInstance
	 */
	public function Get($id)
	{
		$eventInstance = $this->eventInstanceRepo->find($id);
		$this->validationContextAssert($eventInstance);
		
		return $eventInstance;		
	}
	
	
	/**
	 * @param Day $day
	 * @return Doctrine\Common\Collections\ArrayCollection
	 */
	public function GetByDay(Day $day)
	{
		$this->validationContextAssert($day);
		return $this->eventInstanceRepo->findByDay($day);
	}
	
	
	/**
	 * @param Period $period
	 * @return Doctrine\Common\Collections\ArrayCollection
	 */
	public function GetByPeriod(Period $period)
	{
		$this->validationContextAssert($period);
		return $this->eventInstanceRepo->findByPeriod($period);
	}
	
	
	/**
	 * @return Doctrine\Common\Collections\ArrayCollection
	 */
	public function GetByCamp()
	{
		$camp = $this->getContext()->getCamp();
		return $this->eventInstanceRepo->findByCamp($camp);
	}
	
	
	/**
	 * @param Period $period
	 * @param Event $event
	 * @param DateTime|DateInterval|int $start
	 * @param DateInterval|int $duration
	 * @return EventInstance
	 */
	public function Create(Period $period, Event $event, $start, $duration)
	{
		$this->validationContextAssert($period);
		$this->validationContextAssert($event);
		
		if($start instanceof \DateTime){
			$start = $period->getStart()->diff($start);
		}
		
		$eventInstance = new EventInstance($event);
		$eventInstance->setPeriod($period);
		$eventInstance->setOffset($start);
		$eventInstance->setDuration($duration);
		
		$this->validationAssert($eventInstance->getOffsetInMinutes() >= 0,
			"It is not allowed, that the EventInstance starts before Period.");
		
		$this->validationAssert($eventInstance->getEndTime() <= $period->getEnd(),
			"It is not allowed, that the EventInstance ends after Period.");
		
		$this->persist($eventInstance);
		return $eventInstance;
	}
	
	
	/**
	 * @param EventInstance $eventInstance
	 * @param Period $period
	 * @param DateTime|DateInterval|int $start
	 */
	public function Move(EventInstance $eventInstance, Period $period , $start)
	{
		$this->validationContextAssert($period);
		$this->validationContextAssert($eventInstance);
		
		if($start instanceof \DateTime){
			$start = $period->getStart()->diff($start);
		}
		
		$eventInstance->setPeriod($period);
		$eventInstance->setOffset($start);
	}
	
	
	/**
	 * @param EventInstance $eventInstance
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
		
		if($event->getEventInstances()->count() == 0){
			// TODO: What to do with Event without any EventInstnace any more?
		}
		
		$this->remove($eventInstance);
	}
	
}
