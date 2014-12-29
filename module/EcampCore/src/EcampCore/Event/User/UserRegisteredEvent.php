<?php

namespace EcampCore\Event\User;

use EcampCore\Entity\User;
use EcampCore\Event\UserEvent;

class UserRegisteredEvent extends UserEvent
{
    const UserRegistered = 'user-registered';

    public function __construct($target, User $user){
        parent::__construct(self::UserRegistered, $target, $user);
    }

}