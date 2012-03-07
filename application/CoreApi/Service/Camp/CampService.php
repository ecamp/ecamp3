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
	public function Create(\Zend_Form $form)
	{
		$this->blockIfInvalid(parent::Create($form));
		
		$camp = new CoreCamp();
		
		$campValidator = new CampValidator($camp);
		$campValidator->applyIfValid($form);
		
		return $camp->asReadonly();
	}
	
	
	/**
	 * @return CoreApi\Entity\Camp
	 */
	public function CreatePeriod($camp, \Zend_Form $form)
	{
		$this->blockIfInvalid(parent::CreatePeriod($camp, $form));
		
		$camp = $this->GetCoreCamp($camp);
		
		$period = new Period($camp);
		$this->persistEntity($period);
		
		$periodValidator = new PeriodValidator($period);
		$periodValidator->applyIfValid($form);
		
		return $camp->asReadonly();
	}
	
}