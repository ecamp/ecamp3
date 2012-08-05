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

use Core\Service\ServiceBase;

use CoreApi\Entity\Day;
use CoreApi\Entity\Camp;
use CoreApi\Entity\Period;
use CoreApi\Service\Params\Params;


class PeriodService extends ServiceBase
{
	
	
	/**
	 * Setup ACL
	 * @return void
	 */
	public function _setupAcl()
	{}
	
	public function Create(Camp $camp, Params $params)
	{
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
			$day = new Day($period, $offset);
			$this->persist($day);
		}
		
		return $period;
	}
	
	public function Update(Period $period, Params $params)
	{
		$this->validationContextAssert($period);
		
		if($params->hasElement('description')){
			$period->setDescription($params->getValue('description'));
		}
	}
	
	public function Delete(Period $period)
	{
		// How to handle EventsInstances of this Period??
	}
	
	public function Move(Period $period, $newStart)
	{
		if(! $newStart instanceof \DateTime){
			$newStart = new \DateTime($newStart, new \DateTimeZone("GMT"));
		}
		
		$period->setStart($newStart);
	}
	
	public function Resize(Period $period, $numOfDays)
	{
		$oldNumOfDays = $period->getNumberOfDays();
		
		if($oldNumOfDays < $numOfDays){
			for($offset = $oldNumOfDays; $offset < $numOfDays; $offset++){
				$day = new Day($period, $offset);
				$this->persist($day);
			}
			
			return;
		}
		
		if($oldNumOfDays > $numOfDays){
			for($offset = $numOfDays; $offset < $oldNumOfDays; $offset++){
				$this->deleteDay($period, $offset);
			}
		}
	}
	
	private function deleteDay(Period $period, $offset){
		// How to handle EventInstances fo this Day?
	}
	
}