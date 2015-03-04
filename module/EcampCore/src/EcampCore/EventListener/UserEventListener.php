<?php

namespace EcampCore\EventListener;

use EcampCore\Event\UserEvent;
use EcampCore\Job\SendActivationMailJob;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class UserEventListener extends AbstractListenerAggregate
{

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(UserEvent::UserRegistered, array($this, 'onUserRegistered'));
        $this->listeners[] = $events->attach(UserEvent::UserActivated, array($this, 'onUserActivated'));
        $this->listeners[] = $events->attach(UserEvent::UserDeleted, array($this, 'onUserDeleted'));
        $this->listeners[] = $events->attach(UserEvent::UserPasswordResetRequested, array($this, 'onUserPasswordResetRequested'));
        $this->listeners[] = $events->attach(UserEvent::UserPasswordChanged, array($this, 'onUserPasswordChanged'));
    }


    public function onUserRegistered(UserEvent $event)
    {
        SendActivationMailJob::Create($event->getUser());
    }

    public function onUserActivated(UserEvent $event)
    {
        // TODO: Send Welcome-Mail
    }

    public function onUserDeleted(UserEvent $event)
    {
        // TODO: Send Conformation-Mail, that all User-Information is deleted
    }

    public function onvPasswordResetRequested(UserEvent $event)
    {
        // TODO: Send Password-Reset-Link by Mail
    }

    public function onUserPasswordChanged(UserEvent $event)
    {
        // TODO: Send Password-Changed-Mail
    }

}