<?php

namespace EcampCore\Event;

use EcampCore\Entity\CampCollaboration;
use EcampCore\Event\CampCollaboration\CollaborationInvitationCreatedEvent;
use Zend\EventManager\Event;

class CampCollaborationEvent extends Event {

    const CollaborationInvitationCreated = 'collaboration-invitation-created';
    const CollaborationInvitationRevoked = 'collaboration-invitation-revoked';
    const CollaborationInvitationAccepted = 'collaboration-invitation-accepted';
    const CollaborationInvitationRejected = 'collaboration-invitation-rejected';

    const CollaborationRequestCreated = 'collaboration-request-created';
    const CollaborationRequestRevoked = 'collaboration-request-revoked';
    const CollaborationRequestAccepted = 'collaboration-request-accepted';
    const CollaborationRequestRejected = 'collaboration-request-rejected';

    const CollaborationUserKicked = 'collaboration-user-kicked';
    const CollaborationCampLeft = 'collaboration-camp-left';


    public static function InvitationCreated($target, CampCollaboration $campCollaboration){
        return new self(self::CollaborationInvitationCreated, $target, $campCollaboration);
    }
    public static function InvitationRevoked($target, CampCollaboration $campCollaboration){
        return new self(self::CollaborationInvitationRevoked, $target, $campCollaboration);
    }
    public static function InvitationAccepted($target, CampCollaboration $campCollaboration){
        return new self(self::CollaborationInvitationAccepted, $target, $campCollaboration);
    }
    public static function InvitationRejected($target, CampCollaboration $campCollaboration){
        return new self(self::CollaborationInvitationRejected, $target, $campCollaboration);
    }

    public static function RequestCreated($target, CampCollaboration $campCollaboration){
        return new self(self::CollaborationInvitationCreated, $target, $campCollaboration);
    }
    public static function RequestRevoked($target, CampCollaboration $campCollaboration){
        return new self(self::CollaborationInvitationRevoked, $target, $campCollaboration);
    }
    public static function RequestAccepted($target, CampCollaboration $campCollaboration){
        return new self(self::CollaborationInvitationAccepted, $target, $campCollaboration);
    }
    public static function RequestRejected($target, CampCollaboration $campCollaboration){
        return new self(self::CollaborationInvitationRejected, $target, $campCollaboration);
    }

    public static function UserKicked($target, CampCollaboration $campCollaboration){
        return new self(self::CollaborationUserKicked, $target, $campCollaboration);
    }
    public static function CampLeft($target, CampCollaboration $campCollaboration){
        return new self(self::CollaborationCampLeft, $target, $campCollaboration);
    }


    /**
     * @var CampCollaboration
     */
    protected $campCollaboration;

    public function __construct($name, $target, CampCollaboration $campCollaboration){
        parent::__construct($name, $target);

        $this->campCollaboration = $campCollaboration;
    }

    /**
     * @return CampCollaboration
     */
    public function getCampCollaboration(){
        return $this->campCollaboration;
    }

}