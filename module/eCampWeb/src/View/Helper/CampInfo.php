<?php

namespace eCamp\Web\View\Helper;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\EntityService\CampService;

class CampInfo
{
    /** @var CampService */
    private $campService;

    /** @var Camp */
    private $camp;

    public function __construct(CampService $campService, $campId)
    {
        $this->campService = $campService;

        if ($campId instanceof Camp) {
            $campId = $campId->getId();
        }

        $this->camp = $campService->fetch($campId);
    }


    public function countCollaborationRequests()
    {
        return $this->camp->getCampCollaborations()->filter(function ($c) {
            /** @var CampCollaboration $c */
            return $c->isRequest();
        })->count();
    }
}
