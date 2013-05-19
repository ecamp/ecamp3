<?php

namespace EcampCore\Acl;

use EcampCore\Entity\User;
use EcampCore\Entity\Group;
use EcampCore\Entity\Camp;
use EcampCore\Entity\BaseEntity;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;

class Resource implements ResourceInterface
{
	const USER 	= 'User';
	const GROUP = 'Group';
	const CAMP 	= 'Camp';
	
	public static function Create(BaseEntity $entity){
		$resource = self::getResource($entity);
		
		if($resource){
			return new self($entity, $resource);
		} else {
			return null;
		}
	}
	
	private static function getResource(BaseEntity $entity){
		if(
			$entity instanceof User ||
			$entity instanceof Group ||
			$entity instanceof Camp
		){
			return $entity;
		}
		
		if($entity instanceof BelongsToUser){
			return $entity->getUser();
		}
		if($entity instanceof BelongsToGroup){
			return $entity->getGroup();
		}
		if($entity instanceof BelongsToCamp){
			return $entity->getCamp();
		}
		
		return null;
	}
	
	
	
	private $entity;	
	private $resource;
	
	protected function __construct($entity, $resource){
		$this->entity = $entity;
		$this->resource = $resource;
	}
	
	public function getEntity(){
		return $this->entity;
	}
	
	public function getResourceId(){
		
		if($this->resource instanceof User){
			return "EcampCore\Entity\User::" . $this->resource->getId();
		}
		if($this->resource instanceof Group){
			return "EcampCore\Entity\Group::" . $this->resource->getId();
		}
		if($this->resource instanceof Camp){
			return "EcampCore\Entity\Camp::" . $this->resource->getId();
		}
		
		return null;
	}
	
	public function register(Acl $acl){
		if(! $acl->hasResource($this)){
			$acl->addResource($this, $this->getParentResource());
		}
	}
	
	private function getParentResource(){
		
		if($this->resource instanceof User){
			return self::USER;
		}
		if($this->resource instanceof Group){
			return self::GROUP;
		}
		if($this->resource instanceof Camp){
			return self::CAMP;
		}
		
		return null;
	}
}