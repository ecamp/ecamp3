<?php

namespace EcampCore\Acl\Resource;

use EcampCore\Entity\Group;
use Zend\Permissions\Acl\Resource\ResourceInterface;

class GroupResource
	implements ResourceInterface
{
	/**
	 * @var Group
	 */
	private $group;
	
	/**
	 * @param Group $group
	 */
	public function __construct(Group $group){
		$this->group = $group;
	}
	
	/**
	 * @return Group
	 */
	public function getGroup(){
		return $this->group;
	}
	
	
	public function getResourceId(){
		return 'EcampCore\Entity\Group::' . $this->group->getId();
	}
	
}