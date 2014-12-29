<?php

namespace EcampCore\Event\CampCollaboration;

use EcampCore\Entity\CampCollaboration;
use EcampCore\Event\CampCollaborationEvent;

class CollaborationInvitationCreatedEvent extends CampCollaborationEvent  {

    const CollaborationInvitationCreated = 'collaboration-invitation-created';

    public function __construct($target, CampCollaboration $CampCollaboration){
        parent::__construct(self::CollaborationInvitationCreated, $target, $CampCollaboration);
    }

}