<?php
namespace EcampApi\V1\Rest\Period;

use ZF\Apigility\Doctrine\Server\Resource\DoctrineResource;
use EcampCore\Entity\Day;

class PeriodResource extends DoctrineResource
{
	public function create($data){
		$period = parent::create($data);

		$this->updateDays($period, $data);
		
		return $period;
	}
	
	public function update($id,$data){
		$period = parent::update($id, $data);
		
		$this->updateDays($period, $data);
		
		return $period;
	}
	
	public function patch($id, $data){
		$period = parent::patch($id, $data);
		
		$this->updateDays($period, $data);
		
		return $period;
	}
	
	public function delete($id){
		return parent::delete($id);
	}
	
	
	private function updateDays($period, $data)
	{
		
		$start = $period->getStart();
		$end   = new \DateTime($data->end);
		
		if(isset($end)){
			$numOfDays = ($end->getTimestamp() - $start->getTimestamp())/(24 * 60 * 60) + 1;
			$oldNumOfDays = $period->getDays()->count();
			
			
			if ($oldNumOfDays < $numOfDays) {
				for ($offset = $oldNumOfDays; $offset < $numOfDays; $offset++) {
					//$this->dayService->AppendDay($period);
					$day = new Day($period, $offset);
					$period->getDays()->add($day);
					$this->getObjectManager()->persist($day);
				}
			}
			
			if ($oldNumOfDays > $numOfDays) {
				for ($offset = $numOfDays; $offset < $oldNumOfDays; $offset++) {
					$period->getDays()->removeElement( $period->getDays()->last() );
				}
			}
			
		}
		
		$this->getObjectManager()->flush();
		
	}
}
