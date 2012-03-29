<?php

namespace CoreApi\Service;

use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;

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
		$this->acl->allow(DefaultAcl::GROUP_MEMBER, $this, 'CreateCamp');
		$this->acl->allow(DefaultAcl::GROUP_MEMBER, $this, 'UpdateCamp');
		$this->acl->allow(DefaultAcl::GROUP_MEMBER, $this, 'DeleteCamp');
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
		if(isset($id))
		{	$group = $this->groupRepo->find($id);	}
		else
		{	$group = $this->contextProvider->getContext()->getGroup();	}
		
		return $group;
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
	 * Creates a new Camp
	 * @return \CoreApi\Entity\Camp
	 */
	public function CreateCamp(\Zend_Form $form)
	{
		/* check if camp with same name already exists */
		if( ! $this->isCampNameUnique($form->getValue("name")) )
		{
			$form->getElement('name')->addError("Camp with same name already exists.");
			$this->validationFailed();
		}

		/* create camp */
		$camp = $this->campService->Create($form);
		$camp->setGroup($this->contextProvider->getContext()->getGroup());
		
		return $camp;
	}
	
	/**
	 * Updates a Camp
	 * @return \CoreApi\Entity\Camp
	 */
	public function UpdateCamp(\Zend_Form $form)
	{
		$camp = $this->campService->Get($form->getValue('id'));
		
		if($camp->getGroup() != $this->contextProvider->getContext()->getGroup())
			throw new \Exception("No Access");
		
		if( $form->getValue('name') != $camp->getName() )
		{
			if( ! $this->isCampNameUnique($form->getValue("name")) )
			{
				$form->getElement('name')->addError("Camp with same name already exists.");
				$this->validationFailed();
			}
		}
	
		/* update camp */
		$camp = $this->campService->Update($camp, $form);
	
		return $camp;
	}
	
	public function DeleteCamp($id)
	{
		$camp = $this->campService->Get($id);
		
		if( $camp == null )
			return false;
		
		if($camp->getGroup() != $this->contextProvider->getContext()->getGroup())
			throw new \Exception("No Access");
		
		$this->campService->Delete($camp);
		
		return true;
	}
	
	/**
	 * @return bool
	 */
	private function isCampNameUnique($name)
	{
		/* check if camp with same name already exists */
		$qb = $this->em->createQueryBuilder();
		$qb->add('select', 'c')
		->add('from', '\CoreApi\Entity\Camp c')
		->add('where', 'c.group = ?1 AND c.name = ?2')
		->setParameter(1,$this->contextProvider->getContext()->getGroup()->getId())
		->setParameter(2, $name);
		
		$query = $qb->getQuery();
		
		if( count($query->getArrayResult()) > 0 ){
			return false;
		}
		
		return true;
	}
	
}