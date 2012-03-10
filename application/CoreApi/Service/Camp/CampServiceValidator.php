<?php

namespace CoreApi\Service\Camp;

use Core\Entity\BaseEntity;
use CoreApi\Service\ServiceBase;
use CoreApi\Service\ValidationResponse;

use Core\Entity\Camp as CoreCamp;
use CoreApi\Entity\Camp as CoreApiCamp;


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
		return new ValidationResponse(true);
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
	
	
	/**
	 * @return Core\Entity\Camp 
	 */
	protected function GetCoreCamp($id)
	{
		if(is_numeric($id))
		{	return $this->campRepo->find($id);	}
			
		if($id instanceof CoreCamp)
		{	return $id;	}
		
		if($id instanceof CoreApiCamp)
		{	return $this->UnwrapEntity($id);	}
		
		return null;
	}
	
}