<?php

namespace EcampCore\Event\CampCollaboration;

use EcampCore\Entity\CampCollaboration;
use EcampCore\Event\CampCollaborationEvent;

class CollaborationInvitationAcceptedEvent extends CampCollaborationEvent  {

    const CollaborationInvitationAccepted = 'collaboration-invitation-accepted';

    public function __construct($target, CampCollaboration $CampCollaboration){
        parent::__construct(self::CollaborationInvitationAccepted, $target, $CampCollaboration);
    }

}