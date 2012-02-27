<?php

namespace CoreApi\Service\Camp;

use Core\Entity\BaseEntity;
use CoreApi\Service\ServiceBase;


class CampServiceValidator
	extends ServiceBase
{
	
	/**
	 * @var Core\Repository\CampRepository
	 * @Inject Core\Repository\CampRepository
	 */
	protected $campRepo;
	
	
	/**
	 * @return bool
	 */
	public function Get($id)
	{
		return true;
	}
	
	
	/**
	 * @return bool
	 */
	public function Delete($camp)
	{
		return true;
	}
	
	
	/**
	 * @return bool 
	 */
	public function Update($camp, \Zend_Form $form)
	{
		return true;
	}
	
	
	/**
	 * @return bool 
	 */
	public function Create(\Zend_Form $form)
	{
		return true;
	}
	
	
	/**
	 * @return bool 
	 */
	public function CreatePeriod()
	{
		return true;
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