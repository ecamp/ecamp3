<?php

namespace CoreApi\Service;

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
		$form = $this->getForm("Create");
		
		if( !$form->isValid($params) ) {
			throw new \Ecamp\ValidationException($form);
		}
		
		$camp->setCreator($creator);
		
		$period = new \Core\Entity\Period($camp);
		$form->getData($camp, $period);
		
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
