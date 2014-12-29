<?php

namespace EcampCore\Event\CampCollaboration;

use EcampCore\Entity\CampCollaboration;
use EcampCore\Event\CampCollaborationEvent;

class CollaborationRequestRevokedEvent extends CampCollaborationEvent  {

    const CollaborationRequestRevoked = 'collaboration-request-revoked';

    public function __construct($target, CampCollaboration $CampCollaboration){
        parent::__construct(self::CollaborationRequestRevoked, $target, $CampCollaboration);
    }

}