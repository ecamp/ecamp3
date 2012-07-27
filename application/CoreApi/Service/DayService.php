<?php

namespace CoreApi\Service;


use CoreApi\Entity\Period;

use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;
use CoreApi\Service\Params\Params;

use CoreApi\Entity\Period;
use CoreApi\Entity\Day;


/**
 * @method CoreApi\Service\LoginService Simulate
 */
class DayService 
	extends ServiceBase
{
	
	/**
	 * @var CoreApi\Service\UserService
	 * @Inject Core\Service\UserService
	 */
	protected $userService;
	
	
	/**
	 * @var Core\Repository\LoginRepository
	 * @Inject Core\Repository\LoginRepository
	 */
	protected $loginRepo;
	
	
	/**
	 * Setup ACL
	 * @return void
	 */
	public function _setupAcl()
	{
		$this->acl->allow(DefaultAcl::CAMP_MEMBER, $this, 'AppendDay');
		$this->acl->allow(DefaultAcl::CAMP_MEMBER, $this, 'RemoveDay');
		$this->acl->allow(DefaultAcl::CAMP_MEMBER, $this, 'Update');
	}
	
	
	/**
	 * @param Period $period
	 * @return Day
	 */
	public function AppendDay(Period $period)
	{
		$camp = $this->getContext()->getCamp();
		
		$this->validationAssert($camp == $period->getCamp(),
			"Period does not belong to Camp of Context!");
		
		
		
		$day = new Day();
		$day->setDayOffset($period->getNumberOfDays());
		
		$period->getDays()->add($day);
		$day->setPeriod($period);
		
		$this->persist($day);
		return $day;
	}
	
	
	/**
	 * @param Period $period
	 */
	public function RemoveDay(Period $period)
	{
		$camp = $this->getContext()->getCamp();
		
		$this->validationAssert($camp == $period->getCamp(), 
			"Period does not belong to Camp of Context!");
		
		
		// Can the day be deletet?
		// What about the EventInstances?
		
		$day = $period->getDays()->last();
		$period->getDays()->removeElement($day);
		
		$this->remove($day);
	}
	
	
	/**
	 * @param Day $day
	 * @param Params $param
	 */
	public function Update(Day $day, Params $param)
	{
		$camp = $this->getContext()->getCamp();
		
		$this->validationAssert($camp == $day->getPeriod()->getCamp(),
			"Day does not belong to Camp of Context!");
		
		
		if($params->hasElement('notes')){
			$day->setNotes($param->getValue('notes'));
		}
	}
	
}
