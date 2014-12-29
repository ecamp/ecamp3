<?php

namespace EcampCore\EventListener;

use EcampCore\Event\User\PasswordChangedEvent;
use EcampCore\Event\User\PasswordResetRequestedEvent;
use EcampCore\Event\User\UserActivatedEvent;
use EcampCore\Event\User\UserDeletedEvent;
use EcampCore\Event\User\UserRegisteredEvent;
use EcampCore\Job\SendActivationMailJob;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class UserEventListener extends AbstractListenerAggregate
{

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(UserRegisteredEvent::UserRegistered, array($this, 'onUserRegistered'));
        $this->listeners[] = $events->attach(UserActivatedEvent::UserActivated, array($this, 'onUserActivated'));
        $this->listeners[] = $events->attach(PasswordResetRequestedEvent::PasswordResetRequested, array($this, 'onPasswordResetRequested'));
        $this->listeners[] = $events->attach(PasswordChangedEvent::PasswordChanged, array($this, 'onPasswordChanged'));
        $this->listeners[] = $events->attach(UserDeletedEvent::UserDeleted, array($this, 'onUserDeleted'));
    }


    public function onUserRegistered(UserRegisteredEvent $event)
    {
        SendActivationMailJob::Create($event->getUser());
    }

    public function onUserActivated(UserActivatedEvent $event)
    {
        // TODO: Send Welcome-Mail
    }

    public function onPasswordResetRequested(PasswordResetRequestedEvent $event)
    {
        // TODO: Send Password-Reset-Link by Mail
    }

    public function onPasswordChanged(PasswordChangedEvent $event)
    {
        // TODO: Send Password-Changed-Mail
    }

    public function onUserDeleted(UserDeletedEvent $event)
    {
        // TODO: Send Conformation-Mail, that all User-Information is deleted
    }

}