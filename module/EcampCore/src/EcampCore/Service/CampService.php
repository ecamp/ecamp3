<?php

namespace EcampCore\Service;


use EcampCore\Acl\DefaultAcl;
use EcampCore\Validator\Entity\CampValidator;

use EcampCore\Entity\Camp;
use EcampCore\Entity\Period;

use EcampCore\Service\Params\Params;

/**
 * @method EcampCore\Service\CampService Simulate
 */
class CampService
	extends ServiceBase
{
	
	/**
	 * Setup ACL
	 * @return void
	 */
	public function _setupAcl(){
		$this->getAcl()->allow(DefaultAcl::MEMBER, $this, 'Get');
		$this->getAcl()->allow(DefaultAcl::MEMBER, $this, 'Create');
		$this->getAcl()->allow(DefaultAcl::CAMP_CREATOR, $this, 'Delete');
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
		{	return $this->repo()->campRepository()->find($id);	}
			
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
			if($this->repo()->campRepository()->findPersonalCamp($user->getId(), $campName) != null){
				$params->addError('name', "Camp with same name already exists.");
				$this->validationFailed();
			}
			
			$camp->setOwner($user);
		}
		else{
			
			// Create group Camp
			if($this->repo()->campRepository()->findGroupCamp($group->getId(), $campName) != null){
				$params->addError('name', "Camp with same name already exists.");
				$this->validationFailed();
			}
			
			$camp->setGroup($group);
		}
		
		$camp->setCreator($user);
		
		$campValidator = new CampValidator($camp);
		$this->validationFailed( !$campValidator->applyIfValid($params) );
		
		$this->service()->periodService()->CreatePeriodForCamp($camp, $params);
		
		return $camp;
	}
	
}