<?php

namespace EcampCore\EventListener;

use EcampCore\Event\GroupMembershipEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class GroupMembershipEventListener extends AbstractListenerAggregate
{

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(GroupMembershipEvent::MembershipRequestCreated, array($this, 'onMembershipRequestCreated'));
        $this->listeners[] = $events->attach(GroupMembershipEvent::MembershipRequestRevoked, array($this, 'onMembershipRequestRevoked'));
        $this->listeners[] = $events->attach(GroupMembershipEvent::MembershipRequestAccepted, array($this, 'onMembershipRequestAccepted'));
        $this->listeners[] = $events->attach(GroupMembershipEvent::MembershipRequestRejected, array($this, 'onMembershipRequestRejected'));
        $this->listeners[] = $events->attach(GroupMembershipEvent::MembershipInvitationCreated, array($this, 'onMembershipInvitationCreated'));
        $this->listeners[] = $events->attach(GroupMembershipEvent::MembershipInvitationRevoked, array($this, 'onMembershipInvitationRevoked'));
        $this->listeners[] = $events->attach(GroupMembershipEvent::MembershipInvitationAccepted, array($this, 'onMembershipInvitationAccepted'));
        $this->listeners[] = $events->attach(GroupMembershipEvent::MembershipInvitationRejected, array($this, 'onMembershipInvitationRejected'));
        $this->listeners[] = $events->attach(GroupMembershipEvent::MembershipGroupLeft, array($this, 'onMembershipGroupLeft'));
        $this->listeners[] = $events->attach(GroupMembershipEvent::MembershipUserKicked, array($this, 'onMembershipUserKicked'));
    }

    public function onMembershipRequestCreated(GroupMembershipEvent $event){
    }

    public function onMembershipRequestRevoked(GroupMembershipEvent $event){
    }

    public function onMembershipRequestAccepted(GroupMembershipEvent $event){
    }

    public function onMembershipRequestRejected(GroupMembershipEvent $event){
    }

    public function onMembershipInvitationCreated(GroupMembershipEvent $event){
        $user = $event->getGroupMembership()->getUser();
        if($user->getSettings()->getSendCampInvitations()){
            // TODO: Send Info-Mail, You have been invited to Camp
        }
    }

    public function onMembershipInvitationRevoked(GroupMembershipEvent $event){
    }

    public function onMembershipInvitationAccepted(GroupMembershipEvent $event){
    }

    public function onMembershipInvitationRejected(GroupMembershipEvent $event){
    }

    public function onMembershipGroupLeft(GroupMembershipEvent $event){
    }

    public function onMembershipUserKicked(GroupMembershipEvent $event){
    }

}