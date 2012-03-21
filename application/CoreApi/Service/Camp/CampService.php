<?php

namespace CoreApi\Service\Camp;

use Core\Validator\Entity\CampValidator;

use Core\Acl\DefaultAcl;

use Core\Entity\Period;

use Core\Entity\Camp as CoreCamp;

use Core\Service\ServiceBase;

class CampService
	extends ServiceBase
{
	/**
	 * Setup ACL
	 * @return void
	 */
	protected function _setupAcl()
	{
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Create');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Delete');
	}
	
	/**
	 * @return Core\Entity\Camp | NULL
	 */
	public function Get($id)
	{
		$this->blockIfInvalid(parent::Get($id));
		
		
		if(is_numeric($id))
		{	return $this->Get($this->campRepo->find($id));	}
			
		if($id instanceof CoreCamp)
		{	return $id;	}
		
		return null;
	}
	
	
	
	public function Delete($camp)
	{
		$this->blockIfInvalid(parent::Delete($camp));
		
		$camp = $this->GetCoreCamp($camp);
		$this->removeEntity($camp);
	}
	
	
	/**
	 * @return Core\Entity\Camp
	 */
	public function Update($camp, \Zend_Form $form)
	{
		$this->blockIfInvalid(parent::Update($camp, $form));
		
		$camp = $this->GetCoreCamp($camp);
		
		$campValidator = new CampValidator($camp);
		$campValidator->applyIfValid($form);
		
		return $camp;
	}
	
	
	/**
	 * @return Core\Entity\Camp
	 */
	public function Create(\Zend_Form $form, $s=false)
	{	
		$t = $this->beginTransaction();
		
		$camp = new CoreCamp();
		$this->persist($camp);
		
		$camp->setCreator($this->context->getMe());
		
		$campValidator = new CampValidator($camp);
		$this->validationFailed( !$campValidator->applyIfValid($form) );
		
		$period = $this->CreatePeriod($camp, $form, $s);
		
		$t->flushAndCommit($s);
		
		return $camp;
	}
	
	
	/**
	 * @return Core\Entity\Camp
	 */
	public function CreatePeriod($camp, \Zend_Form $form, $s=false)
	{
		$t = $this->beginTransaction();
		
		$camp = $this->GetCoreCamp($camp);
		$period = new \Core\Entity\Period($camp);
		$this->persist($period);
		
		if( $form->getValue('from') == "" ){
			$form->getElement('from')->addError("Date cannot be empty.");
			$this->validationFailed();
		} 
		
		if( $form->getValue('to') == "" ){
			$form->getElement('to')->addError("Date cannot be empty.");
			$this->validationFailed();
		}
		
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
	
	/**
	 * @return Core\Entity\Camp
	 */
	protected function GetCoreCamp($id)
	{
		if(is_numeric($id))
		{
			return $this->campRepo->find($id);
		}
			
		if($id instanceof CoreCamp)
		{
			return $id;
		}
	
		return null;
	}
}