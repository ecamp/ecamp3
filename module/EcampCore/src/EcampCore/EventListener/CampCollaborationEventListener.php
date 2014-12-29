<?php

namespace EcampCore\EventListener;

use EcampCore\Event\CampCollaboration\CollaborationCampLeftEvent;
use EcampCore\Event\CampCollaboration\CollaborationInvitationAcceptedEvent;
use EcampCore\Event\CampCollaboration\CollaborationInvitationCreatedEvent;
use EcampCore\Event\CampCollaboration\CollaborationInvitationRejectedEvent;
use EcampCore\Event\CampCollaboration\CollaborationInvitationRevokedEvent;
use EcampCore\Event\CampCollaboration\CollaborationRequestAcceptedEvent;
use EcampCore\Event\CampCollaboration\CollaborationRequestCreatedEvent;
use EcampCore\Event\CampCollaboration\CollaborationRequestRejectedEvent;
use EcampCore\Event\CampCollaboration\CollaborationRequestRevokedEvent;
use EcampCore\Event\CampCollaboration\CollaborationUserKickedEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class CampCollaborationEventListener extends AbstractListenerAggregate
{

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(CollaborationRequestCreatedEvent::CollaborationRequestCreated, array($this, 'onCollaborationRequestCreated'));
        $this->listeners[] = $events->attach(CollaborationRequestRevokedEvent::CollaborationRequestRevoked, array($this, 'onCollaborationRequestRevoked'));
        $this->listeners[] = $events->attach(CollaborationRequestAcceptedEvent::CollaborationRequestAccepted, array($this, 'onCollaborationRequestAccepted'));
        $this->listeners[] = $events->attach(CollaborationRequestRejectedEvent::CollaborationRequestRejected, array($this, 'onCollaborationRequestRejected'));
        $this->listeners[] = $events->attach(CollaborationInvitationCreatedEvent::CollaborationInvitationCreated, array($this, 'onCollaborationInvitationCreated'));
        $this->listeners[] = $events->attach(CollaborationInvitationRevokedEvent::CollaborationInvitationRevoked, array($this, 'onCollaborationInvitationRevoked'));
        $this->listeners[] = $events->attach(CollaborationInvitationAcceptedEvent::CollaborationInvitationAccepted, array($this, 'onCollaborationInvitationAccepted'));
        $this->listeners[] = $events->attach(CollaborationInvitationRejectedEvent::CollaborationInvitationRejected, array($this, 'onCollaborationInvitationRejected'));
        $this->listeners[] = $events->attach(CollaborationCampLeftEvent::CollaborationCampLeft, array($this, 'onCollaborationCampLeft'));
        $this->listeners[] = $events->attach(CollaborationUserKickedEvent::CollaborationUserKicked, array($this, 'onCollaborationUserKicked'));
    }

    public function onCollaborationRequestCreated(CollaborationRequestCreatedEvent $event){
    }

    public function onCollaborationRequestRevoked(CollaborationRequestRevokedEvent $event){
    }

    public function onCollaborationRequestAccepted(CollaborationRequestAcceptedEvent $event){
    }

    public function onCollaborationRequestRejected(CollaborationRequestRejectedEvent $event){
    }

    public function onCollaborationInvitationCreated(CollaborationInvitationCreatedEvent $event){
    }

    public function onCollaborationInvitationRevoked(CollaborationInvitationRevokedEvent $event){
    }

    public function onCollaborationInvitationAccepted(CollaborationInvitationAcceptedEvent $event){
    }

    public function onCollaborationInvitationRejected(CollaborationInvitationRejectedEvent $event){
    }

    public function onCollaborationCampLeft(CollaborationCampLeftEvent $event){
    }

    public function onCollaborationUserKicked(CollaborationUserKickedEvent $event){
    }

}