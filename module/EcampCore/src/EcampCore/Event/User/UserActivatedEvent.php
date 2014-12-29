<?php

namespace EcampCore\Event\User;

use EcampCore\Entity\User;
use EcampCore\Event\UserEvent;

class UserActivatedEvent extends UserEvent
{
    const UserActivated = 'user-activated';

    public function __construct($target, User $user){
        parent::__construct(self::UserActivated, $target, $user);
    }

}