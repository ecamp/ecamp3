<?php

namespace CoreApi\Service;

use Core\Acl\DefaultAcl;
use Core\Entity\Period;
use Core\Service\ServiceBase;
use Core\Validator\Entity\CampValidator;


class CampService
	extends ServiceBase
{
	/**
	 * @var \Repository\CampRepository
	 * @Inject CampRepository
	 */
	private $campRepo;
	
	/**
	 * Setup ACL
	 * @return void
	 */
	protected function _setupAcl()
	{
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Create');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Delete');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Get');
	}
	
	/**
	 * @return Core\Entity\Camp | NULL
	 */
	public function Get($id)
	{
		if(is_numeric($id))
		{	return $this->Get($this->campRepo->find($id));	}
			
		if($id instanceof \Core\Entity\Camp)
		{	return $id;	}
		
		return null;
	}
	
	
	
	public function Delete($camp, $s = false)
	{
		$t = $this->beginTransaction();
		
		$camp = $this->Get($camp);
		$this->removeEntity($camp);
		
		$t->flushAndCommit($s);
	}
	
	
	/**
	 * @return Core\Entity\Camp
	 */
	public function Update($camp, \Zend_Form $form, $s = false)
	{
		$t = $this->beginTransaction();
		
		$camp = $this->Get($camp);
		$campValidator = new CampValidator($camp);
		
		$this->validationFailed(
			$campValidator->applyIfValid($form));
		
		$t->flushAndCommit($s);
		
		return $camp;
	}
	
	
	/**
	 * @return Core\Entity\Camp
	 */
	public function Create(\Zend_Form $form, $s = false)
	{	
		$t = $this->beginTransaction();
		
		$camp = new \Core\Entity\Camp();
		$this->persist($camp);
		
		$camp->setCreator($this->contextProvider->getContext()->getMe());
		
		$campValidator = new CampValidator($camp);
		$this->validationFailed( !$campValidator->applyIfValid($form) );
		
		$period = $this->CreatePeriod($camp, $form, $s);
		
		$t->flushAndCommit($s);
		
		return $camp;
	}
	
	
	/**
	 * @return Core\Entity\Camp
	 */
	public function CreatePeriod($camp, \Zend_Form $form, $s = false)
	{
		$t = $this->beginTransaction();
		
		if( $form->getValue('from') == "" ){
			$form->getElement('from')->addError("Date cannot be empty.");
			$this->validationFailed();
		} 
		
		if( $form->getValue('to') == "" ){
			$form->getElement('to')->addError("Date cannot be empty.");
			$this->validationFailed();
		}
		
		
		$camp = $this->Get($camp);
		$period = new \Core\Entity\Period($camp);
		$this->persist($period);
		
		$from = new \DateTime($form->getValue('from'), new \DateTimeZone("GMT"));
		$to   = new \DateTime($form->getValue('to'), new \DateTimeZone("GMT"));
		
		$period->setStart($from);
		$period->setDuration(($to->getTimestamp() - $from->getTimestamp())/(24 * 60 * 60) + 1);
		
		if( $period->getDuration() < 1){
			$form->getElement('to')->addError("Minimum length of camp is 1 day.");
			$this->validationFailed();
		}
		
		$t->flushAndCommit($s);
		
		return $period;
	}
	
}