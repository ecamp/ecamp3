<?php

namespace EcampCore\Event\CampCollaboration;

use EcampCore\Entity\CampCollaboration;
use EcampCore\Event\CampCollaborationEvent;

class CollaborationInvitationRejectedEvent extends CampCollaborationEvent  {

    const CollaborationInvitationRejected = 'collaboration-invitation-rejected';

    public function __construct($target, CampCollaboration $CampCollaboration){
        parent::__construct(self::CollaborationInvitationRejected, $target, $CampCollaboration);
    }

}