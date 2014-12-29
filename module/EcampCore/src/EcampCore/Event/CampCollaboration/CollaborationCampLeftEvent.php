<?php

namespace EcampCore\Event\CampCollaboration;

use EcampCore\Entity\CampCollaboration;
use EcampCore\Event\CampCollaborationEvent;

class CollaborationCampLeftEvent extends CampCollaborationEvent  {

    const CollaborationCampLeft = 'collaboration-camp-left';

    public function __construct($target, CampCollaboration $CampCollaboration){
        parent::__construct(self::CollaborationCampLeft, $target, $CampCollaboration);
    }

}