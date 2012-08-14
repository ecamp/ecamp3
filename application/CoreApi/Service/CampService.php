<?php

namespace CoreApi\Service;

use CoreApi\Service\Params\Params;

use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;
use Core\Validator\Entity\CampValidator;

use CoreApi\Entity\Camp;
use CoreApi\Entity\Period;


/**
 * @method \CoreApi\Service\CampService Simulate
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
	 * @var CoreApi\Service\PeriodService
	 * @Inject Core\Service\PeriodService
	 */
	private $periodService;
	
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
		{	return $this->getContext()->getCamp();	}
		
		if(is_string($id))
		{	return $this->campRepo->find($id);	}
			
		if($id instanceof Camp)
		{	return $id;	}
		
		return null;
	}
	
	
	/**
	 * Deletes the current Camp
	 */
	public function Delete()
	{
		$camp = $this->getContext()->getCamp();
		$this->remove($camp);
	}
	
	
	/**
	 * Updates the current Camp
	 * 
	 * @return CoreApi\Entity\Camp
	 */
	public function Update(Params $params)
	{
		$camp = $this->getContext()->getCamp();
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
		$group = $this->getContext()->getGroup();
		$user  = $this->getContext()->getMe();
		$campName =  $params->getValue('name');
		
		$camp = new Camp();
		$this->persist($camp);
		
		if($group == null){
			
			// Create group Camp
			if($this->campRepo->findUserCamp($user, $campName) != null){
				$params->addError('name', "Camp with same name already exists.");
				$this->validationFailed();
			}
			
			$camp->setOwner($user);
		}
		else{
			
			// Create personal Camp
			if($this->campRepo->findGroupCamp($group, $campName) != null){
				$params->addError('name', "Camp with same name already exists.");
				$this->validationFailed();
			}
			
			$camp->setGroup($group);
		}
		
		$camp->setCreator($user);
		
		$campValidator = new CampValidator($camp);
		$this->validationFailed( !$campValidator->applyIfValid($params) );
		
		$this->periodService->CreatePeriodForCamp($camp, $params);
		
		return $camp;
	}
	
}