<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace CoreApi\Service;

use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;

use CoreApi\Entity\Day;
use CoreApi\Entity\Camp;
use CoreApi\Entity\Period;
use CoreApi\Service\Params\Params;


class PeriodService extends ServiceBase
{
	
	/**
	 * @var CoreApi\Service\DayService
	 * @Inject Core\Service\DayService
	 */
	private $dayService;
	
	
	/**
	 * Setup ACL
	 * @return void
	 */
	public function _setupAcl()
	{
		$this->acl->allow(DefaultAcl::CAMP_MEMBER, $this, 'Create');
		$this->acl->allow(DefaultAcl::CAMP_MEMBER, $this, 'Update');
		$this->acl->allow(DefaultAcl::CAMP_MEMBER, $this, 'Delete');
		$this->acl->allow(DefaultAcl::CAMP_MEMBER, $this, 'Move');
		$this->acl->allow(DefaultAcl::CAMP_MEMBER, $this, 'Resize');
	}
	
	
	/**
	 * @param Camp $camp
	 * @param Params $params
	 * @return CoreApi\Entity\Period
	 */
	public function Create(Params $params)
	{
		$camp = $this->getContext()->getCamp();
		
		if( $params->getValue('start') == ""){
			$params->addError('start', "Date cannot be empty.");
			$this->validationFailed();
		}
		
		if( $params->getValue('end') == "" ){
			$params->addError('end', "Date cannot be empty.");
			$this->validationFailed();
		}
		
		
		$period = new Period($camp);
		$this->persist($period);
		
		$start = new \DateTime($params->getValue('start'), new \DateTimeZone("GMT"));
		$end   = new \DateTime($params->getValue('end'), new \DateTimeZone("GMT"));
		$desc  = $params->hasElement('description') ? $params->getValue('description') : "";
		
		
		$period->setStart($start);
		$period->setDescription($desc);
		
		$numOfDays = ($end->getTimestamp() - $start->getTimestamp())/(24 * 60 * 60) + 1;
		if( $numOfDays < 1){
			$params->addError('end', "Minimum length of camp is 1 day.");
			$this->validationFailed();
		}
		
		for($offset = 0; $offset < $numOfDays; $offset++){
			$this->dayService->AppendDay($period);
		}
		
		return $period;
	}
	
	
	/**
	 * @param Period $period
	 * @param Params $params
	 */
	public function Update(Period $period, Params $params)
	{
		$camp = $this->getContext()->getCamp();
		
		$this->validationAssert($camp == $period->getCamp(),
			"Period does not belong to Camp of Context!");
		
		if($params->hasElement('description')){
			$period->setDescription($params->getValue('description'));
		}
	}
	
	
	/**
	 * @param Period $period
	 */
	public function Delete(Period $period)
	{
		$camp = $this->getContext()->getCamp();
		
		$this->validationAssert($camp == $period->getCamp(), 
			"Period does not belong to Camp of Context!");
		
		// How to handle EventsInstances of this Period??
	}
	
	
	/**
	 * @param Period $period
	 * @param unknown_type $newStart
	 */
	public function Move(Period $period, $newStart)
	{
		$camp = $this->getContext()->getCamp();
		
		$this->validationAssert($camp == $period->getCamp(),
			"Period does not belong to Camp of Context!");
		
		
		if(! $newStart instanceof \DateTime){
			$newStart = new \DateTime($newStart, new \DateTimeZone("GMT"));
		}
		
		$period->setStart($newStart);
	}
	
	
	/**
	 * @param Period $period
	 * @param int $numOfDays
	 */
	public function Resize(Period $period, $numOfDays)
	{
		$camp = $this->getContext()->getCamp();
		
		$this->validationAssert($camp == $period->getCamp(),
			"Period does not belong to Camp of Context!");
		
		
		$oldNumOfDays = $period->getNumberOfDays();
		
		if($oldNumOfDays < $numOfDays){
			for($offset = $oldNumOfDays; $offset < $numOfDays; $offset++){
				$this->dayService->AppendDay($period);
			}
			
			return;
		}
		
		if($oldNumOfDays > $numOfDays){
			for($offset = $numOfDays; $offset < $oldNumOfDays; $offset++){
				$this->dayService->RemoveDay($period);
			}
		}
	}
	
}