<?php

namespace EcampApi\Serializer;

use EcampCore\Entity\Period;
use EcampCore\Entity\Day;

class DaySerializer extends BaseSerializer{
	
	public function serialize($day){
		$periodSerializer = new PeriodSerializer($this->format, $this->router);
		
		return array(
    		'id' 		=> 	$day->getId(),
			'href'		=>	$this->getDayHref($day),
			'period'	=> 	$periodSerializer->getReference($day->getPeriod()),
			'date'		=> 	$day->getStart()->getTimestamp(),
			'notes'		=> 	$day->getNotes()
		);
	}
	
	public function getReference(Day $day = null){
		if($day == null){
			return null;
		} else {
			return array(
				'id'	=>	$day->getId(),
				'href'	=>	$this->getDayHref($day)
			);
		}
	}
	
	public function getCollectionReference($collectionOwner){
		if($collectionOwner instanceof Period){
			return array('href' => $this->getPeriod_DayCollectionHref($collectionOwner));
		}
		
		return null;
	}
	
	private function getDayHref(Day $day){
		return
			$this->router->assemble(
				array(
					'controller' => 'day',
					'action' => 'get',
					'id' => $day->getId(),
					'format' => $this->format
				), 
				array(
					'name' => 'api/default'
				)
		);
	}
	
	private function getPeriod_DayCollectionHref(Period $period){
		return 
			$this->router->assemble(
				array(
					'controller' => 'day',
					'action' => 'getList',
					'period' => $period->getId(),
					'format' => $this->format
				),
				array(
					'name' => 'api/default'
				)
			);
	}
}