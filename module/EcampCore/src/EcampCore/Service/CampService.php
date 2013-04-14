<?php

namespace EcampCore\Service;

use CoreApi\Service\Params\Params;

use Core\Acl\DefaultAcl;
use Core\Validator\Entity\CampValidator;

use EcampCore\Entity\Camp;
use EcampCore\Entity\Period;


/**
 * @method \CoreApi\Service\CampService Simulate
 */
class CampService
	extends ServiceBase
{
	/**
	 * @return EcampCore\Repository\CampRepository
	 */
	private function getCampRepo(){
		return $this->locateService('ecamp.repo.camp');
	}
	
	/**
	 * @return EcampCore\Service\PeriodService
	 */
	private function getPeriodService(){
		return $this->locateService('ecamp.service.period');
	}

	
	/**
	 * Setup ACL
	 * @return void
	 */
	public function _setupAcl()
	{
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Get');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Create');
		$this->acl->allow(DefaultAcl::CAMP_CREATOR, $this, 'Delete');
	}
	
	
	/**
	 * Returns the requested Camp
	 * 
	 * For an integer the Camp with the given id
	 * For NULL the Camp from the Context
	 * 
	 * @return CoreApi\Entity\Camp | NULL
	 */
	public function Get($id = null)
	{
		if(is_null($id))
		{	return $this->getContextProvider()->getCamp();	}
		
		if(is_string($id))
		{	return $this->getCampRepo()->find($id);	}
			
		if($id instanceof Camp)
		{	return $id;	}
		
		return null;
	}
	
	
	/**
	 * Deletes the current Camp
	 */
	public function Delete()
	{
		$camp = $this->getContextProvider()->getCamp();
		$this->remove($camp);
	}
	
	
	/**
	 * Updates the current Camp
	 * 
	 * @return CoreApi\Entity\Camp
	 */
	public function Update(Params $params)
	{
		$camp = $this->getContextProvider()->getCamp();
		$campValidator = new CampValidator($camp);
		
		$this->validationFailed(
			!$campValidator->applyIfValid($params));
		
		return $camp;
	}
	
	
	/**
	 * Creats a new Camp
	 * Whether the camp belongs to a user or to a group 
	 * depends on the Context.
	 * 
	 * If the Group is set in the Context, 
	 * it belongs to this Group.
	 * Otherwise, the camp belongs to the authenticated User.
	 * 
	 * @return CoreApi\Entity\Camp
	 */
	public function Create(Params $params)
	{
		$group = $this->getContextProvider()->getGroup();
		$user  = $this->getContextProvider()->getMe();
		$campName =  $params->getValue('name');
		
		$camp = new Camp();
		$this->persist($camp);
		
		if($group == null){
			
			// Create personal Camp
			if($this->getCampRepo()->findPersonalCamp($user->getId(), $campName) != null){
				$params->addError('name', "Camp with same name already exists.");
				$this->validationFailed();
			}
			
			$camp->setOwner($user);
		}
		else{
			
			// Create group Camp
			if($this->getCampRepo()->findGroupCamp($group->getId(), $campName) != null){
				$params->addError('name', "Camp with same name already exists.");
				$this->validationFailed();
			}
			
			$camp->setGroup($group);
		}
		
		$camp->setCreator($user);
		
		$campValidator = new CampValidator($camp);
		$this->validationFailed( !$campValidator->applyIfValid($params) );
		
		$this->getPeriodService()->CreatePeriodForCamp($camp, $params);
		
		return $camp;
	}
	
}