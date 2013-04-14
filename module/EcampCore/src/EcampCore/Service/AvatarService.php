<?php

namespace EcampCore\Service;

use EcampCore\Acl\DefaultAcl;

use EcampCore\Entity\Image;


/**
 * @method CoreApi\Service\CampService Simulate
 */
class AvatarService
	extends ServiceBase
{
	/**
	 * @return EcampCore\Service\UserService
	 */
	private function getUserService(){
		return $this->locateService('ecamp.service.user');
	}
	
	/**
	 * @return EcampCore\Service\GroupService
	 */
	private function getGroupService(){
		return $this->locateService('ecamp.service.group');
	}
	
	/**
	 * Setup ACL
	 * @return void
	 */
	public function _setupAcl()
	{
// 		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Create');
// 		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Delete');
// 		$this->acl->allow(DefaultAcl::MEMBER, $this, 'Get');
	}
	
	/**
	 * @return CoreApi\Entity\Image
	 */
	public function GetUserAvatar($userId)
	{
		$user = $this->getUserService()->Get($userId);
		$image = $user->getImage();
		
		if($image == null)
		{
			$image = new Image();
			$image->setMime("image/png");
			$image->setData(file_get_contents(APPLICATION_PATH . "/../public/img/default_avatar.png"));
		}
		
		return $image;
	}
	
	/**
	 * @return CoreApi\Entity\Image
	 */
	public function GetGroupAvatar($groupId)
	{
		$group = $this->getGroupService()->Get($groupId);
		$image = $group->getImage();
		
		if($image == null)
		{
			$image = new Image();
			$image->setMime("image/png");
			$image->setData(file_get_contents(APPLICATION_PATH . "/../public/img/default_group.png"));
		}
		
		return $image;
	}
	
}