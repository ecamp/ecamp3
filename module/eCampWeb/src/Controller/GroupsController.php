<?php

namespace eCamp\Web\Controller;

use eCamp\Core\EntityServiceAware\GroupServiceAware;
use eCamp\Core\EntityServiceTrait\GroupServiceTrait;

class GroupsController extends AbstractBaseController
    implements GroupServiceAware {
    use GroupServiceTrait;


    public function indexAction() {
        $userGroups = $this->getGroupService()->fetchByUser();
        $rootGroups = $this->getGroupService()->fetchByParentGroup(null);

        return [
            'userGroups' => $userGroups,
            'rootGroups' => $rootGroups
        ];
    }
}
