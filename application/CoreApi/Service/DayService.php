<?php

namespace CoreApi\Service;


use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;
use CoreApi\Service\Params\Params;

use CoreApi\Entity\Period;
use CoreApi\Entity\Day;


/**
 * @method CoreApi\Service\DayService Simulate
 */
class DayService 
	extends ServiceBase
{
	
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
		$this->validationContextAssert($period);
		
		
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
		$this->validationContextAssert($period);
		
		
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
		$this->validationContextAssert($day);
		
		
		if($param->hasElement('notes')){
			$day->setNotes($param->getValue('notes'));
		}
	}
	
}
