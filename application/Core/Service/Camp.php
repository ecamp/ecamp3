<?php

namespace Core\Service;

class Camp extends ServiceAbstract
{

	/**
	 * @var \Doctrine\ORM\EntityManager
	 * @Inject EntityManager
	 */
	protected $em;
	
    // public function index(){}
	public function get(){}
	public function update(){}
	public function delete(){}
	
	
	public function create(\Core\Entity\User $creator, $params)
	{
		$camp = new \Core\Entity\Camp();
				    	
		$camp->setCreator($creator);
		
		if(isset($params['name']))
		{	$camp->setName($params['name']);	}
		
		if(isset($params['title']))
		{	$camp->setTitle($params['title']);	}
		
		$period = $this->createPeriod($camp, $params);
		
		$this->em->persist($camp);
		$this->em->persist($period);
		
		return $camp;
	}
	
	private function createPeriod(\Core\Entity\Camp $camp, $params)
	{
		$period = new \Core\Entity\Period($camp);
	
		$from = new \DateTime($params['from'], new \DateTimeZone("GMT"));
		$to   = new \DateTime($params['to'], new \DateTimeZone("GMT"));
		
		$period->setStart($from);
		$period->setDuration(($to->getTimestamp() - $from->getTimestamp())/(24 * 60 * 60) + 1);
		
		return $period;
	}
	
}
