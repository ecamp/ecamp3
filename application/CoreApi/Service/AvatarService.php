<?php

namespace CoreApi\Service;


use CoreApi\Entity\Image;

use Core\Acl\DefaultAcl;
use Core\Service\ServiceBase;



/**
 * @method CoreApi\Service\CampService Simulate
 */
class AvatarService
	extends ServiceBase
{
	/**
	 * @var CoreApi\Service\UserService
	 * @Inject Core\Service\UserService
	 */
	private $userService;
	
	/**
	 * @var CoreApi\Service\GroupService
	 * @Inject Core\Service\GroupService
	 */
	private $groupService;
	
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
		$user = $this->userService->Get($userId);
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
		$group = $this->groupService->Get($groupId);
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