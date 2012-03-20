<?php

namespace CoreApi\Service\Camp;

use Core\Entity\BaseEntity;
use Core\Service\ServiceBase;



use Core\Validator\Entity\CampValidator;


class CampServiceValidator
	extends ServiceBase
{
	
	/**
	 * @var Core\Repository\CampRepository
	 * @Inject Core\Repository\CampRepository
	 */
	protected $campRepo;
	
	
	/**
	 * @return ValidationResponse
	 */
	public function Get($id)
	{
		return new ValidationResponse(true);
	}
	
	
	/**
	 * @return ValidationResponse
	 */
	public function Delete($camp)
	{
		return new ValidationResponse(true);;
	}
	
	
	/**
	 * @return ValidationResponse 
	 */
	public function Update($camp, \Zend_Form $form)
	{
		return new ValidationResponse(true);
	}
	
	
	/**
	 * @return ValidationResponse 
	 */
	public function Create(\Core\Entity\User $creator, \Zend_Form $form)
	{
		$camp = new CoreCamp();
		$camp->setCreator($creator);
		
		$campValidator = new CampValidator($camp);
		if( !$campValidator->isValid($form) )
			return new ValidationResponse(false);
		
		return self::CreatePeriod($camp, $form);
	}
	
	
	/**
	 * @return ValidationResponse 
	 */
	public function CreatePeriod($camp, \Zend_Form $form)
	{
		$from = new \DateTime($form->getValue('from'), new \DateTimeZone("GMT"));
		$to   = new \DateTime($form->getValue('to'), new \DateTimeZone("GMT"));
		
		$duration = ($to->getTimestamp() - $from->getTimestamp())/(24 * 60 * 60) + 1;
		
		if( $duration < 1){
			$form->getElement('to')->addError("Minimum length of camp is 1 day.");
			return new ValidationResponse(false);
		}
		
		return new ValidationResponse(true);
	}
	
	

	
}