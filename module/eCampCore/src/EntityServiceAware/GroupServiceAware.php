<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface GroupServiceAware {
    /**
     * @return EntityService\GroupService
     */
    public function getGroupService();

    /**
     * @param EntityService\GroupService $groupService
     */
    public function setGroupService(EntityService\GroupService $groupService);
}
