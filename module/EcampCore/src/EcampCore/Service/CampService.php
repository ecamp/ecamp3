<?php

namespace EcampCore\Service;

use EcampCore\Acl\Resource;

use EcampCore\Acl\Assertion\UserSelf;
use EcampCore\Acl\Assertion\GroupMember;
use EcampCore\Acl\Assertion\CampMember;
use EcampCore\Acl\Assertion\CampManager;

use EcampCore\Entity\User;
use EcampCore\Entity\Group;
use EcampCore\Entity\Camp;
use EcampCore\Entity\CampOwnerInterface;
use EcampCore\Validator\Entity\CampValidator;

use EcampLib\Service\Params\Params;

use EcampLib\Service\ServiceBase;
use EcampCore\Repository\CampRepository;

/**
 * @method EcampCore\Service\CampService Simulate
 */
class CampService
	extends ServiceBase
{
	/** @var CampRepository */
	private $campRepo;
	
	/** @var PeriodService */
	private $periodService;
	
	
	public function __construct(
		CampRepository $campRepo,
		PeriodService $periodService
	){
		$this->campRepo = $campRepo;
		$this->periodService = $periodService;
	}
	
	
	/**
	 * Returns the requested Camp
	 * 
	 * For an integer the Camp with the given id
	 * For NULL the Camp from the Context
	 * 
	 * @return CoreApi\Entity\Camp | NULL
	 */
	public function Get($id){
		$camp = null;
		
		if(is_string($id)){
			$camp = $this->campRepo->find($id);
		}
			
		if($id instanceof Camp){
			$camp = $id;
		}
		
		if($camp != null){
			$this->aclRequire($this->me(), $camp, __CLASS__.'::'.__METHOD__);
			return $camp;
		}
		
		return null;
	}
	
	
	/**
	 * Deletes the current Camp
	 */
	public function Delete(Camp $camp){
		$this->aclRequire($this->me(), $camp, 'administrate');
		
		$this->remove($camp);
	}
	
	
	/**
	 * Updates the current Camp
	 * 
	 * @return CoreApi\Entity\Camp
	 */
	public function Update(Camp $camp, Params $params){
		
		$this->aclRequire($this->me(), $camp, __CLASS__.'::'.__METHOD__);
		
		$campValidator = new CampValidator($camp);
		
		$this->validationFailed(
			!$campValidator->applyIfValid($params));
		
		return $camp;
	}
	
	
	/**
	 * Creats a new Camp
	 * If Group is defined, the Camp belongs to this Group.
	 * Otherwise it belongs to the User.
	 * 
	 * @return CoreApi\Entity\Camp
	 */
	public function Create(CampOwnerInterface $owner, Params $params){
		
		$this->aclRequire($this->me(), $owner , __CLASS__.'::'.__METHOD__);
		
		$campName =  $params->getValue('name');

		$camp = new Camp();
		$this->persist($camp);
		
		if($owner instanceof User){
			// Create personal Camp
			if($this->campRepo->findPersonalCamp($owner->getId(), $campName) != null){
				$params->addError('name', "Camp with same name already exists.");
				$this->validationFailed();
			}
			$camp->setOwner($owner);
		} 
		elseif($owner instanceof Group) {
			// Create group Camp
			if($this->campRepo->findGroupCamp($owner->getId(), $campName) != null){
				$params->addError('name', "Camp with same name already exists.");
				$this->validationFailed();
			}
			$camp->setGroup($owner);
		}
		
		$camp->setCreator($this->me());
		
		$campValidator = new CampValidator($camp);
		$this->validationFailed( !$campValidator->applyIfValid($params) );
		
		$this->periodService->CreatePeriodForCamp($camp, $params);
		
		return $camp;
	}
	
}