<?php

namespace EcampCore\EventListener;

use EcampCore\Event\GroupMembership\MembershipGroupLeftEvent;
use EcampCore\Event\GroupMembership\MembershipInvitationAcceptedEvent;
use EcampCore\Event\GroupMembership\MembershipInvitationCreatedEvent;
use EcampCore\Event\GroupMembership\MembershipInvitationRejectedEvent;
use EcampCore\Event\GroupMembership\MembershipInvitationRevokedEvent;
use EcampCore\Event\GroupMembership\MembershipRequestAcceptedEvent;
use EcampCore\Event\GroupMembership\MembershipRequestCreatedEvent;
use EcampCore\Event\GroupMembership\MembershipRequestRejectedEvent;
use EcampCore\Event\GroupMembership\MembershipRequestRevokedEvent;
use EcampCore\Event\GroupMembership\MembershipUserKickedEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class GroupMembershipEventListener extends AbstractListenerAggregate
{

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MembershipRequestCreatedEvent::MembershipRequestCreated, array($this, 'onMembershipRequestCreated'));
        $this->listeners[] = $events->attach(MembershipRequestRevokedEvent::MembershipRequestRevoked, array($this, 'onMembershipRequestRevoked'));
        $this->listeners[] = $events->attach(MembershipRequestAcceptedEvent::MembershipRequestAccepted, array($this, 'onMembershipRequestAccepted'));
        $this->listeners[] = $events->attach(MembershipRequestRejectedEvent::MembershipRequestRejected, array($this, 'onMembershipRequestRejected'));
        $this->listeners[] = $events->attach(MembershipInvitationCreatedEvent::MembershipInvitationCreated, array($this, 'onMembershipInvitationCreated'));
        $this->listeners[] = $events->attach(MembershipInvitationRevokedEvent::MembershipInvitationRevoked, array($this, 'onMembershipInvitationRevoked'));
        $this->listeners[] = $events->attach(MembershipInvitationAcceptedEvent::MembershipInvitationAccepted, array($this, 'onMembershipInvitationAccepted'));
        $this->listeners[] = $events->attach(MembershipInvitationRejectedEvent::MembershipInvitationRejected, array($this, 'onMembershipInvitationRejected'));
        $this->listeners[] = $events->attach(MembershipGroupLeftEvent::MembershipGroupLeft, array($this, 'onMembershipGroupLeft'));
        $this->listeners[] = $events->attach(MembershipUserKickedEvent::MembershipUserKicked, array($this, 'onMembershipUserKicked'));
    }

    public function onMembershipRequestCreated(MembershipRequestCreatedEvent $event){
    }

    public function onMembershipRequestRevoked(MembershipRequestRevokedEvent $event){
    }

    public function onMembershipRequestAccepted(MembershipRequestAcceptedEvent $event){
    }

    public function onMembershipRequestRejected(MembershipRequestRejectedEvent $event){
    }

    public function onMembershipInvitationCreated(MembershipInvitationCreatedEvent $event){
        $user = $event->getGroupMembership()->getUser();
        if($user->getSettings()->getSendCampInvitations()){
            // TODO: Send Info-Mail, You have been invited to Camp
        }
    }

    public function onMembershipInvitationRevoked(MembershipInvitationRevokedEvent $event){
    }

    public function onMembershipInvitationAccepted(MembershipInvitationAcceptedEvent $event){
    }

    public function onMembershipInvitationRejected(MembershipInvitationRejectedEvent $event){
    }

    public function onMembershipGroupLeft(MembershipGroupLeftEvent $event){
    }

    public function onMembershipUserKicked(MembershipUserKickedEvent $event){
    }

}