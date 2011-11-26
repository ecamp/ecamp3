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
	public function delete(){}
	
	public function get($id)
	{
		$camp = $this->em->getRepository("Core\Entity\Camp")->find($id);
		return $camp;
	}
	
	protected function update($params)
	{
		$id = $params["id"];
		$camp = $this->em->getRepository("Core\Entity\Camp")->find($id);
		
		$form = new \Core\Form\Camp\Update();
		
		if( !$form->isValid($params) )
			throw new \Ecamp\ValidationException($form);
		
		$form->getData($camp);
		
		$this->em->persist($camp);
		
		return $camp;
	}
	
	protected function create(\Core\Entity\User $creator, $params)
	{
		$camp = new \Core\Entity\Camp();
		$form = new \Core\Form\Camp\Create();
		
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
	
	/**
	* Setup ACL. Is used for manual calls of 'checkACL' and for automatic checking
	* @see    CoreApi\Service\ServiceAbstract::_setupAcl()
	* @return void
	*/
	protected function _setupAcl()
	{
		$this->_acl->allow('camp_owner', $this, 'create');
		$this->_acl->allow('camp_owner', $this, 'update');
	}
	
}
