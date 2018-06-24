<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface GroupMembershipServiceAware {
    /**
     * @return EntityService\GroupMembershipService
     */
    public function getGroupMembershipService();

    /**
     * @param EntityService\GroupMembershipService $groupMembershipService
     */
    public function setGroupMembershipService(EntityService\GroupMembershipService $groupMembershipService);
}
