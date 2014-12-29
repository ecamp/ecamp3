<?php

namespace EcampCore\Event\CampCollaboration;

use EcampCore\Entity\CampCollaboration;
use EcampCore\Event\CampCollaborationEvent;

class CollaborationUserKickedEvent extends CampCollaborationEvent  {

    const CollaborationUserKicked = 'collaboration-user-kicked';

    public function __construct($target, CampCollaboration $CampCollaboration){
        parent::__construct(self::CollaborationUserKicked, $target, $CampCollaboration);
    }

}