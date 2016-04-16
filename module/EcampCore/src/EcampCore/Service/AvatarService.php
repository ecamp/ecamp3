<?php

namespace EcampCore\Service;

use EcampCore\Entity\Image;

class AvatarService extends Base\ServiceBase
{
    /** @var UserService */
    private $userService;

    /** @var GroupService */
    private $groupService;

    public function __construct(
        $userService,
        $groupService
    ){
        $this->userService = $userService;
        $this->groupService = $groupService;
    }

    /**
     * @return \EcampCore\Entity\Image
     */
    public function GetUserAvatar($userId)
    {
        $user = $this->userService->Get($userId);
        $image = $user->getImage();

        return $image ?: $this->loadDefaultUserImage();
    }

    /**
     * @return \EcampCore\Entity\Image
     */
    public function GetGroupAvatar($groupId)
    {
        $group = $this->groupService->Get($groupId);
        $image = $group->getImage();

        return $image ?: $this->loadDefaultGroupImage();
    }

    private function loadDefaultUserImage()
    {
        return new Image(__DIR__ . '/../../../assets/img/avatar.user.png');
    }

    private function loadDefaultGroupImage()
    {
        return new Image(__DIR__ . '/../../../assets/img/avatar.group.png');
    }
}
