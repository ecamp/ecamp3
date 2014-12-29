<?php

namespace EcampCore\Event\CampCollaboration;

use EcampCore\Entity\CampCollaboration;
use EcampCore\Event\CampCollaborationEvent;

class CollaborationRequestRejectedEvent extends CampCollaborationEvent  {

    const CollaborationRequestRejected = 'collaboration-request-rejected';

    public function __construct($target, CampCollaboration $CampCollaboration){
        parent::__construct(self::CollaborationRequestRejected, $target, $CampCollaboration);
    }

}