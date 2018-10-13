<?php

namespace eCamp\Web\Controller;

use eCamp\Core\EntityService\GroupService;

class GroupsController extends AbstractBaseController {

    private $groupService;

    public function __construct(GroupService $groupService) {
        $this->groupService = $groupService;
    }

    public function indexAction() {
        $userGroups = $this->groupService->fetchByUser();
        $rootGroups = $this->groupService->fetchByParentGroup(null);

        return [
            'userGroups' => $userGroups,
            'rootGroups' => $rootGroups
        ];
    }
}
