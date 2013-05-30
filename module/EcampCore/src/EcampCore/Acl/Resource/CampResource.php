<?php

namespace EcampCore\Acl\Resource;

use EcampCore\Entity\Camp;
use Zend\Permissions\Acl\Resource\ResourceInterface;

class CampResource
	implements ResourceInterface
{
	/**
	 * @var Camp
	 */
	private $camp;
	
	/**
	 * @param Camp $camp
	 */
	public function __construct(Camp $camp){
		$this->camp = $camp;
	}
	
	/**
	 * @return Camp
	 */
	public function getCamp(){
		return $this->camp;
	}
	
	
	public function getResourceId(){
		return 'EcampCore\Entity\Camp::' . $this->camp->getId();
	}
	
}