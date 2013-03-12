<?php

namespace CoreApi\Service;

use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;
use CoreApi\Service\Params\Params;

use CoreApi\Entity\Group;

use CoreApi\Entity\GroupRequest;

/**
 * @method CoreApi\Service\GroupService Simulate
 */
class GroupService 
	extends ServiceBase
{
	/**
	 * @var Core\Repository\GroupRepository
	 * @Inject Core\Repository\GroupRepository
	 */
	protected $groupRepo;
	
	/**
	 * @var CoreApi\Service\CampService
	 * @Inject Core\Service\CampService
	 */
	protected $campService;
	
	/**
	 * Setup ACL
	 * @return void
	 */
	public function _setupAcl()
	{
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Get');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'GetRoots');
		$this->acl->allow(DefaultAcl::MEMBER, $this, 'RequestGroup');
	}
	
	/**
	 * Returns the Group with the given ID
	 * 
	 * If no Identifier is given, the Context Group is returned
	 * 
	 * @return CoreApi\Entity\Group
	 */
	public function Get($id = null)
	{
		if(is_null($id))
		{	return $this->contextProvider->getContext()->getGroup();	}
		
		if(is_string($id))
		{	return $this->groupRepo->find($id);	}
		
		if($id instanceof Group)
		{	return $id;	}
		
		return null;
	}
	
	/**
	 * Return all root groups
	 * @return
	 */
	public function GetRoots()
	{
		return $this->groupRepo->createQueryBuilder("g")
					->where("g.parent IS NULL ")
					->getQuery()
					->getResult();
	}
	
	/**
	* Request a new Group
	* @return \CoreApi\Entity\GroupRequest
	*/
	public function RequestGroup(Params $params)
	{
		/* grab parent_group from context */
		$group = $this->contextProvider->getContext()->getGroup();
		
		$new_groupname = $params->getValue("name");
		$me = $this->contextProvider->getContext()->getMe();
		
		/* check if group name is unique in parent_group */
		foreach ( $group->getChildren() as $subgroup ) 
		{
			if ( $subgroup->getName() == $new_groupname ) 
			{
				$params->addError('name', "Group with same name already exists.");
				$this->validationFailed();
			}
		}
		
		// Neue GroupRequest erstellen
		$groupRequest = new GroupRequest();
		// Daten, welche nicht über die $form definiert werden, müssen von Hand gesetzt werden:
		$groupRequest->setRequester($me)->setParent($group);
		
		// GroupValidator erstellen:
		$grouprequestValidator = new \Core\Validator\Entity\GroupRequestValidator($groupRequest);
		
		// Die gemachten Angaben in der $form gegen die neue $groupRequest validieren
		if($grouprequestValidator->isValid($params))
		{
			// und auf die GroupRequest anwenden, wenn diese gültig sind.
			$grouprequestValidator->apply($params);
		
			// die neue und gültie GroupRequest persistieren.
			$this->persist($groupRequest);
		}
		else
		{
			// Wenn die Validierung fehl schlägt, muss dies festgehalten werden:
			$this->validationFailed();
		}
	}
	
}