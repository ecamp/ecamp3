<?php

namespace EcampCore\Event\CampCollaboration;

use EcampCore\Entity\CampCollaboration;
use EcampCore\Event\CampCollaborationEvent;

class CollaborationInvitationRevokedEvent extends CampCollaborationEvent  {

    const CollaborationInvitationRevoked = 'collaboration-invitation-revoked';

    public function __construct($target, CampCollaboration $CampCollaboration){
        parent::__construct(self::CollaborationInvitationRevoked, $target, $CampCollaboration);
    }

}