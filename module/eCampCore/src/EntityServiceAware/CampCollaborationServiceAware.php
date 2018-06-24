<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface CampCollaborationServiceAware {
    /**
     * @return EntityService\CampCollaborationService
     */
    public function getCampCollaborationService();

    /**
     * @param EntityService\CampCollaborationService $campCollaborationService
     */
    public function setCampCollaborationService(EntityService\CampCollaborationService $campCollaborationService);
}
