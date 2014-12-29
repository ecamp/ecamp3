<?php

namespace EcampCore\Event\CampCollaboration;

use EcampCore\Entity\CampCollaboration;
use EcampCore\Event\CampCollaborationEvent;

class CollaborationRequestCreatedEvent extends CampCollaborationEvent  {

    const CollaborationRequestCreated = 'collaboration-request-created';

    public function __construct($target, CampCollaboration $CampCollaboration){
        parent::__construct(self::CollaborationRequestCreated, $target, $CampCollaboration);
    }

}