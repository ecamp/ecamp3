<?php

namespace EcampCore\Event\User;

use EcampCore\Entity\User;
use EcampCore\Event\UserEvent;

class PasswordResetRequestedEvent extends UserEvent {

    const PasswordResetRequested = 'password-reset-requested';

    public function __construct($target, User $user){
        parent::__construct(self::PasswordResetRequested, $target, $user);
    }

}