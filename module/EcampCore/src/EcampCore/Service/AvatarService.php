<?php

namespace EcampCore\Service;

use EcampCore\Entity\Image;
use EcampLib\Service\ServiceBase;

/**
 * @method EcampCore\Service\AvatarService Simulate
 */
class AvatarService
    extends ServiceBase
{
    /** @var UserService */
    private $userService;

    /** @var GroupService */
    private $groupService;

    public function __construct(
        UserService $userService,
        GroupService $groupService
    ){
        $this->userService = $userService;
        $this->groupService = $groupService;
    }

    /**
     * @return EcampCore\Entity\Image
     */
    public function GetUserAvatar($userId)
    {
        $user = $this->userService->Get($userId);
        $image = $user->getImage();

        if ($image == null) {
            $image = new Image();
            $image->setMime("image/png");
            $image->setData(file_get_contents(APPLICATION_PATH . "/../public/img/default_avatar.png"));
        }

        return $image;
    }

    /**
     * @return EcampCore\Entity\Image
     */
    public function GetGroupAvatar($groupId)
    {
        $group = $this->groupService->Get($groupId);
        $image = $group->getImage();

        if ($image == null) {
            $image = new Image();
            $image->setMime("image/png");
            $image->setData(file_get_contents(APPLICATION_PATH . "/../public/img/default_group.png"));
        }

        return $image;
    }

}
