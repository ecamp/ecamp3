<?php

namespace CoreApi\Service;


use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;
use Core\Validator\Entity\CampValidator;

use CoreApi\Entity\Camp;
use CoreApi\Entity\Period;


/**
 * @method CoreApi\Service\CampService Simulate
 */
class CampService
	extends ServiceBase
{
	/**
	 * @var Core\Repository\CampRepository
	 * @Inject CampRepository
	 */
	private $campRepo;
	
	/**
	 * Setup ACL
	 * @return void
	 */
	public function _setupAcl()
	{
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Create');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Delete');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Get');
	}
	
	/**
	 * @return CoreApi\Entity\Camp | NULL
	 */
	public function Get($id = null)
	{
		if(is_null($id))
		{	return $this->contextProvider->getContext()->getCamp();	}
		
		if(is_numeric($id))
		{	return $this->campRepo->find($id);	}
			
		if($id instanceof Camp)
		{	return $id;	}
		
		return null;
	}
	
	
	
	public function Delete($camp)
	{
		$camp = $this->Get($camp);
		$this->remove($camp);
	}
	
	
	/**
	 * @return CoreApi\Entity\Camp
	 */
	public function Update($camp, \Zend_Form $form)
	{
		$camp = $this->Get($camp);
		$campValidator = new CampValidator($camp);
		
		$this->validationFailed(
			!$campValidator->applyIfValid($form));
		
		return $camp;
	}
	
	
	/**
	 * @return CoreApi\Entity\Camp
	 */
	public function Create(\Zend_Form $form)
	{	
		$camp = new Camp();
		$this->persist($camp);
		
		$me = $this->contextProvider->getContext()->getMe();
		$camp->setCreator($me);
		
		$campValidator = new CampValidator($camp);
		$this->validationFailed( !$campValidator->applyIfValid($form) );
		
		$this->CreatePeriod($camp, $form);
		
		return $camp;
	}
	
	
	/**
	 * @return CoreApi\Entity\Camp
	 */
	public function CreatePeriod($camp, \Zend_Form $form)
	{	
		if( $form->getValue('from') == "" ){
			$form->getElement('from')->addError("Date cannot be empty.");
			$this->validationFailed();
		} 
		
		if( $form->getValue('to') == "" ){
			$form->getElement('to')->addError("Date cannot be empty.");
			$this->validationFailed();
		}
		
		
		$camp = $this->Get($camp);
		$period = new Period($camp);
		$this->persist($period);
		
		$from = new \DateTime($form->getValue('from'), new \DateTimeZone("GMT"));
		$to   = new \DateTime($form->getValue('to'), new \DateTimeZone("GMT"));
		
		$period->setStart($from);
		$period->setDuration(($to->getTimestamp() - $from->getTimestamp())/(24 * 60 * 60) + 1);
		
		if( $period->getDuration() < 1){
			$form->getElement('to')->addError("Minimum length of camp is 1 day.");
			$this->validationFailed();
		}
		
		return $period;
	}
	
}