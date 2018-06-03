<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait GroupServiceTrait
{
    /** @var EntityService\GroupService */
    private $groupService;

    public function setGroupService(EntityService\GroupService $groupService) {
        $this->groupService = $groupService;
    }

    public function getGroupService() {
        return $this->groupService;
    }

}
