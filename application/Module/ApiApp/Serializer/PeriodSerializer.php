<?php

namespace ApiApp\Serializer;

use CoreApi\Entity\Camp;
use CoreApi\Entity\Period;

class PeriodSerializer extends BaseSerializer{
	
	public function __invoke(Period $period){
		$campSerializer = new CampSerializer($this->mime);
		$daySerializer = new DaySerializer($this->mime);
		$eventInstanceSerializer = new EventInstanceSerializer($this->mime);
		
		return array(
    		'id' 				=> 	$period->getId(),
			'href'				=>	$this->getPeriodHref($period),
			'start'				=> 	$period->getStart()->getTimestamp(),
			'end'				=> 	$period->getEnd()->getTimestamp(),
			'numDays'			=> 	$period->getNumberOfDays(),
			'camp'				=>  $campSerializer->getReference($period->getCamp()),
			'days'				=>	$daySerializer->getCollectionReference($period),
			'eventInstances'	=> 	$eventInstanceSerializer->getCollectionReference($period),
		);
	}
	
	public function getReference(Period $period = null){
		if($period == null){
			return null;
		} else {
			return array(
				'id'	=>	$period->getId(),
				'href'	=>	$this->getPeriodHref($period)
			);
		}
	}
	
	public function getCollectionReference($collectionOwner){
		if($collectionOwner instanceof Camp){
			return array('href' => $this->getCamp_PeriodCollectionHref($collectionOwner));
		}
		
		return null;
	}
	
	private function getPeriodHref(Period $period){
		return
			$this->router->assemble(
			array(
				'id' => $period->getId(),
				'mime' => $this->mime
			), 
			'api.v1.period'
		);
	}
	
	private function getCamp_PeriodCollectionHref(Camp $camp){
		return 
			$this->router->assemble(
				array(
					'camp' => $camp->getId(),
					'mime' => $this->mime
				),
				'api.v1.camp.period.collection'
			);
	}
}