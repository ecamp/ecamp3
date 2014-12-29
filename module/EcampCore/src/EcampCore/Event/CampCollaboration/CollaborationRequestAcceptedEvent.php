<?php

namespace EcampCore\Event\CampCollaboration;

use EcampCore\Entity\CampCollaboration;
use EcampCore\Event\CampCollaborationEvent;

class CollaborationRequestAcceptedEvent extends CampCollaborationEvent  {

    const CollaborationRequestAccepted = 'collaboration-request-accepted';

    public function __construct($target, CampCollaboration $CampCollaboration){
        parent::__construct(self::CollaborationRequestAccepted, $target, $CampCollaboration);
    }

}