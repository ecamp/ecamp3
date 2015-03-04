<?php

namespace EcampCore\Event;

use EcampCore\Entity\User;
use Zend\EventManager\Event;

class UserEvent extends Event {

    const UserRegistered = 'user-registered';
    const UserActivated = 'user-activated';
    const UserDeleted = 'user-deleted';

    const UserPasswordResetRequested = 'user-password-reset-requested';
    const UserPasswordChanged = 'user-password-changed';


    public static function Registered($target, User $user){
        return new self(self::UserRegistered, $target, $user);
    }
    public static function Activated($target, User $user){
        return new self(self::UserActivated, $target, $user);
    }
    public static function Deleted($target, User $user){
        return new self(self::UserDeleted, $target, $user);
    }

    public static function PasswordResetRequested($target, User $user){
        return new self(self::UserPasswordResetRequested, $target, $user);
    }
    public static function PasswordChanged($target, User $user){
        return new self(self::UserPasswordChanged, $target, $user);
    }


    /**
     * @var User
     */
    protected $user;


    public function __construct($name, $target, User $user){
        parent::__construct($name, $target);

        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(){
        return $this->user;
    }

}