<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait GroupMembershipServiceTrait {
    /** @var EntityService\GroupMembershipService */
    private $groupMembershipService;

    public function setGroupMembershipService(EntityService\GroupMembershipService $groupMembershipService) {
        $this->groupMembershipService = $groupMembershipService;
    }

    public function getGroupMembershipService() {
        return $this->groupMembershipService;
    }
}
