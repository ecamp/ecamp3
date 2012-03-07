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
	public function Create(\Zend_Form $form)
	{
		return new ValidationResponse(true);
	}
	
	
	/**
	 * @return ValidationResponse 
	 */
	public function CreatePeriod()
	{
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
		{	return $this->UnwrappEntity($id);	}
		
		return null;
	}
	
}