<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait CampCollaborationServiceTrait {
    /** @var EntityService\CampCollaborationService */
    private $campCollaborationService;

    public function setCampCollaborationService(EntityService\CampCollaborationService $campCollaborationService) {
        $this->campCollaborationService = $campCollaborationService;
    }

    public function getCampCollaborationService() {
        return $this->campCollaborationService;
    }
}
