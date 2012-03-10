<?php

namespace CoreApi\Service\Camp;

use Core\Entity\Period;

use Core\Entity\Camp as CoreCamp;
use CoreApi\Entity\Camp as CoreApiCamp;

use Core\Validator\Entity\CampValidator;


class CampService
	extends CampServiceValidator
{
	
	/**
	 * @return CoreApi\Entity\Camp | NULL
	 */
	public function Get($id)
	{
		$this->blockIfInvalid(parent::Get($id));
		
		
		if(is_numeric($id))
		{	return $this->Get($this->campRepo->find($id));	}
			
		if($id instanceof CoreCamp)
		{	return $id->asReadonly();	}
		
		if($id instanceof CoreApiCamp)
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
	 * @return CoreApi\Entity\Camp
	 */
	public function Update($camp, \Zend_Form $form)
	{
		$this->blockIfInvalid(parent::Update($camp, $form));
		
		$camp = $this->GetCoreCamp($camp);
		
		$campValidator = new CampValidator($camp);
		$campValidator->applyIfValid($form);
		
		return $camp->asReadonly();
	}
	
	
	/**
	 * @return CoreApi\Entity\Camp
	 */
	public function Create(\Core\Entity\User $creator, \Zend_Form $form, $s=false)
	{	
		$respObj = $this->getRespObj($s)->beginTransaction();
		
		$camp = new CoreCamp();
		$this->persist($camp);
		
		$camp->setCreator($creator);
		
		$campValidator = new CampValidator($camp);
		$respObj->validationFailed( !$campValidator->applyIfValid($form) );
		
		$period = $respObj( $this->CreatePeriod($camp, $form, $s) )->getReturn();
		$period =  $this->UnwrapEntity( $period ); 
		
		$respObj->flushAndCommit();
		return $respObj($camp);
	}
	
	
	/**
	 * @return CoreApi\Entity\Camp
	 */
	public function CreatePeriod($camp, \Zend_Form $form, $s=false)
	{
		$respObj = $this->getRespObj($s)->beginTransaction();
		
		$camp = $this->GetCoreCamp($camp);
		$period = new \Core\Entity\Period($camp);
		$this->persist($period);
		
		if( $form->getValue('from') == "" ){
			$form->getElement('from')->addError("Date cannot be empty.");
			$respObj->validationFailed();
		} 
		
		if( $form->getValue('to') == "" ){
			$form->getElement('to')->addError("Date cannot be empty.");
			$respObj->validationFailed();
		}
		
		$from = new \DateTime($form->getValue('from'), new \DateTimeZone("GMT"));
		$to   = new \DateTime($form->getValue('to'), new \DateTimeZone("GMT"));
		
		$period->setStart($from);
		$period->setDuration(($to->getTimestamp() - $from->getTimestamp())/(24 * 60 * 60) + 1);
		
		if( $period->getDuration() < 1){
			$form->getElement('to')->addError("Minimum length of camp is 1 day.");
			$respObj->validationFailed();
		}
		
		$respObj->flushAndCommit();
		return $respObj($period);
	}
	
}