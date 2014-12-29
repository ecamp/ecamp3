<?php

namespace EcampCore\Event\User;

use EcampCore\Entity\User;
use EcampCore\Event\UserEvent;

class UserDeletedEvent extends UserEvent {

    const UserDeleted = 'user-deleted';

    public function __construct($target, User $user){
        parent::__construct(self::UserDeleted, $target, $user);
    }

}