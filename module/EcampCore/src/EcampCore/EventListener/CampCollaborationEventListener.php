<?php

namespace EcampCore\EventListener;

use EcampCore\Event\CampCollaborationEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class CampCollaborationEventListener extends AbstractListenerAggregate
{

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(CampCollaborationEvent::CollaborationRequestCreated, array($this, 'onCollaborationRequestCreated'));
        $this->listeners[] = $events->attach(CampCollaborationEvent::CollaborationRequestRevoked, array($this, 'onCollaborationRequestRevoked'));
        $this->listeners[] = $events->attach(CampCollaborationEvent::CollaborationRequestAccepted, array($this, 'onCollaborationRequestAccepted'));
        $this->listeners[] = $events->attach(CampCollaborationEvent::CollaborationRequestRejected, array($this, 'onCollaborationRequestRejected'));
        $this->listeners[] = $events->attach(CampCollaborationEvent::CollaborationInvitationCreated, array($this, 'onCollaborationInvitationCreated'));
        $this->listeners[] = $events->attach(CampCollaborationEvent::CollaborationInvitationRevoked, array($this, 'onCollaborationInvitationRevoked'));
        $this->listeners[] = $events->attach(CampCollaborationEvent::CollaborationInvitationAccepted, array($this, 'onCollaborationInvitationAccepted'));
        $this->listeners[] = $events->attach(CampCollaborationEvent::CollaborationInvitationRejected, array($this, 'onCollaborationInvitationRejected'));
        $this->listeners[] = $events->attach(CampCollaborationEvent::CollaborationCampLeft, array($this, 'onCollaborationCampLeft'));
        $this->listeners[] = $events->attach(CampCollaborationEvent::CollaborationUserKicked, array($this, 'onCollaborationUserKicked'));
    }

    public function onCollaborationRequestCreated(CampCollaborationEvent $event){
    }

    public function onCollaborationRequestRevoked(CampCollaborationEvent $event){
    }

    public function onCollaborationRequestAccepted(CampCollaborationEvent $event){
    }

    public function onCollaborationRequestRejected(CampCollaborationEvent $event){
    }

    public function onCollaborationInvitationCreated(CampCollaborationEvent $event){
    }

    public function onCollaborationInvitationRevoked(CampCollaborationEvent $event){
    }

    public function onCollaborationInvitationAccepted(CampCollaborationEvent $event){
    }

    public function onCollaborationInvitationRejected(CampCollaborationEvent $event){
    }

    public function onCollaborationCampLeft(CampCollaborationEvent $event){
    }

    public function onCollaborationUserKicked(CampCollaborationEvent $event){
    }

}