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

use CoreApi\Entity\Camp;
use CoreApi\Entity\Period;


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
		if( $params->getValue('from') == "" ){
			$params->addError('from', "Date cannot be empty.");
			$this->validationFailed();
		}
		
		if( $params->getValue('to') == "" ){
			$params->addError('to', "Date cannot be empty.");
			$this->validationFailed();
		}
		
		
		$period = new Period($camp);
		$this->persist($period);
		
		$from = new \DateTime($params->getValue('from'), new \DateTimeZone("GMT"));
		$to   = new \DateTime($params->getValue('to'), new \DateTimeZone("GMT"));
		
		$period->setStart($from);
		$period->setDuration(($to->getTimestamp() - $from->getTimestamp())/(24 * 60 * 60) + 1);
		
		if( $period->getDuration() < 1){
			$params->addError('to', "Minimum length of camp is 1 day.");
			$this->validationFailed();
		}
		
		return $period;
	}
	
	public function Update(Period $period, Params $params)
	{
		
	}
	
	public function Delete(Period $period)
	{
		
	}
	
	public function Move(Period $period, $newStart)
	{
		
	}
	
	public function Resize(Period $period, $newDuration)
	{
		$period->setDescription($newDuration);
	}
	
}