<?php

namespace ApiApp\Serializer;

use CoreApi\Entity\Period;
use CoreApi\Entity\Day;

class DaySerializer extends BaseSerializer{
	
	public function __invoke(Day $day){
		$periodSerializer = new PeriodSerializer($this->mime);
		
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
				'id' => $day->getId(),
				'mime' => $this->mime
			), 
			'api.v1.day'
		);
	}
	
	private function getPeriod_DayCollectionHref(Period $period){
		return 
			$this->router->assemble(
				array(
					'period' => $period->getId(),
					'mime' => $this->mime
				),
				'api.v1.period.day.collection'
			);
	}
}